<?php

namespace Designbycode\LaravelBrevo;

use Brevo\Client\Api\ContactsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Brevo\Client\Model\AddContactToList;
use Brevo\Client\Model\CreateContact;
use Brevo\Client\Model\GetExtendedContactDetails;
use Brevo\Client\Model\RemoveContactFromList;
use Brevo\Client\Model\UpdateContact;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Brevo
{
    private ContactsApi $contactsApi;

    public function __construct(?ContactsApi $contactsApi = null)
    {
        $this->contactsApi = $contactsApi ?? $this->createDefaultContactsApi();
    }

    private function createDefaultContactsApi(): ContactsApi
    {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', config('brevo.api_key'));

        return new ContactsApi(new Client, $config);
    }

    public function setContactsApi(ContactsApi $contactsApi): void
    {
        $this->contactsApi = $contactsApi;
    }

    public function getContactInfo(string $email): ?GetExtendedContactDetails
    {
        try {
            return $this->contactsApi->getContactInfo($email);
        } catch (ApiException $e) {
            $this->handleApiException($e, 'Contact not found', $e->getCode() === 404);

            return null;
        }
    }

    public function subscribe(string $email, string $listId, array $attributes = []): bool
    {
        try {
            // Check if contact exists
            try {
                $this->contactsApi->getContactInfo($email);

                // Contact exists - update
                $updateContact = new UpdateContact;
                if (! empty($attributes)) {
                    $updateContact->setAttributes((object) $attributes);
                }
                $this->contactsApi->updateContact($email, $updateContact);
            } catch (ApiException $e) {
                if ($e->getCode() == 404) {
                    // Contact doesn't exist - create
                    $createContact = new CreateContact;
                    $createContact->setEmail($email);
                    if (! empty($attributes)) {
                        $createContact->setAttributes((object) $attributes);
                    }
                    $this->contactsApi->createContact($createContact);
                } else {
                    throw $e;
                }
            }

            // Subscribe to list
            $addContactToList = new AddContactToList;
            $addContactToList->setEmails([$email]);
            $this->contactsApi->addContactToList($listId, $addContactToList);

            return true;
        } catch (ApiException $e) {
            Log::error('Brevo API Exception: '.$e->getMessage());

            return false;
        }
    }

    public function unsubscribe(string $email, string $listId): bool
    {
        try {
            $this->removeContactFromList($email, $listId);

            return true;
        } catch (ApiException $e) {
            $success = $e->getCode() === 404;
            $this->handleApiException($e, 'Unsubscribe failed', $success);

            return $success;
        }
    }

    /**
     * @throws \Brevo\Client\ApiException
     */
    private function createOrUpdateContact(string $email, array $attributes): void
    {
        try {
            $this->updateExistingContact($email, $attributes);
        } catch (ApiException $e) {
            if ($e->getCode() === 404) {
                $this->createNewContact($email, $attributes);

                return;
            }
            throw $e;
        }
    }

    /**
     * @throws \Brevo\Client\ApiException
     */
    private function updateExistingContact(string $email, array $attributes): void
    {
        $updateContact = new UpdateContact;

        if (! empty($attributes)) {
            $updateContact->setAttributes((object) $attributes);
        }

        $this->contactsApi->updateContact($email, $updateContact);
    }

    /**
     * @throws \Brevo\Client\ApiException
     */
    private function createNewContact(string $email, array $attributes): void
    {
        $createContact = new CreateContact;
        $createContact->setEmail($email);

        if (! empty($attributes)) {
            $createContact->setAttributes((object) $attributes);
        }

        $this->contactsApi->createContact($createContact);
    }

    /**
     * @throws \Brevo\Client\ApiException
     */
    private function addContactToList(string $email, string $listId): void
    {
        $listRequest = new AddContactToList;
        $listRequest->setEmails([$email]);
        $this->contactsApi->addContactToList($listId, $listRequest);
    }

    /**
     * @throws \Brevo\Client\ApiException
     */
    private function removeContactFromList(string $email, string $listId): void
    {
        $listRequest = new RemoveContactFromList;
        $listRequest->setEmails([$email]);
        $this->contactsApi->removeContactFromList($listId, $listRequest);
    }

    private function handleApiException(ApiException $e, string $context, bool $isWarning = false): void
    {
        $logMethod = $isWarning ? 'warning' : 'error';
        $message = "Brevo API: {$context} - {$e->getMessage()}";

        Log::$logMethod($message, [
            'code' => $e->getCode(),
            'response' => $e->getResponseBody(),
        ]);
    }
}
