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
use phpDocumentor\Reflection\Types\Integer;

class Brevo
{
    private ContactsApi $contactsApi;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', config('brevo.api_key'));
        $this->contactsApi = new ContactsApi(new Client(), $config);
    }

    /**
     * @param string $email
     * @param int $listId
     * @param array $attributes
     *
     * @return bool
     */
    public function subscribe(string $email, int $listId, array $attributes = []): bool
    {
        try {
            // Check if contact exists, update if so, otherwise create
            try {
                $contact = $this->contactsApi->getContactInfo($email);
                $updateContact = new UpdateContact();

                //add or update  attributes
                if (!empty($attributes)) {
                    $updateContact->setAttributes((object)$attributes);
                }

                $this->contactsApi->updateContact($email, $updateContact);


            } catch (ApiException $e) {
                if ($e->getCode() == 404) {
                    $createContact = new CreateContact();
                    $createContact->setEmail($email);
                    //add attributes when creating new contact
                    if (!empty($attributes)) {
                        $createContact->setAttributes((object)$attributes);
                    }
                    $this->contactsApi->createContact($createContact);
                } else {
                    $this->handleApiException($e, 'Contact not found');
                    return false;
                }
            }

            // Subscribe contact to list
            $addContactToList = new AddContactToList();
            $addContactToList->setEmails([$email]);
            $this->contactsApi->addContactToList($listId, $addContactToList);
            return true;

        } catch (ApiException $e) {
            $this->handleApiException($e, 'Subscription failed');
            return false;
        }
    }

    /**
     * @param string $email
     *
     * @return \Brevo\Client\Model\GetExtendedContactDetails|null
     */
    public function getContactInfo(string $email): ?GetExtendedContactDetails
    {
        try {
            return $this->contactsApi->getContactInfo($email);

        } catch (ApiException $e) {
            $this->handleApiException($e, 'Contact not found', $e->getCode() === 404);
            return null;
        }

    }


    /**
     * @param string $email
     * @param int $listId
     *
     * @return bool
     */
    public function unsubscribe(string $email, int $listId): bool
    {
        try {
            $removeContactFromList = new RemoveContactFromList();
            $removeContactFromList->setEmails([$email]);
            $this->contactsApi->removeContactFromList($listId, $removeContactFromList);
            return true;
        } catch (ApiException $e) {
            $this->handleApiException($e, 'Brevo API Warning: User not found in list - ', $e->getCode() === 404);
            return false;
        }
    }


    /**
     * @param \Brevo\Client\ApiException $e
     * @param string $context
     * @param bool $isWarning
     *
     * @return void
     */
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
