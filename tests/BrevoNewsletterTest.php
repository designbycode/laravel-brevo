<?php

use Brevo\Client\Api\ContactsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Model\AddContactToList;
use Brevo\Client\Model\CreateContact;
use Brevo\Client\Model\GetExtendedContactDetails;
use Brevo\Client\Model\RemoveContactFromList;
use Brevo\Client\Model\UpdateContact;
use Designbycode\LaravelBrevo\Brevo;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->contactsApi = Mockery::mock(ContactsApi::class);
    $this->brevo = new Brevo($this->contactsApi);
});

test('retrieves contact information successfully', function () {
    $mockDetails = new GetExtendedContactDetails([
        'email' => 'test@example.com',
        'attributes' => (object) ['name' => 'John Doe'],
    ]);

    $this->contactsApi->shouldReceive('getContactInfo')
        ->once()
        ->with('test@example.com')
        ->andReturn($mockDetails);

    $result = $this->brevo->getContactInfo('test@example.com');

    expect($result)->toBeInstanceOf(GetExtendedContactDetails::class)
        ->and($result->getEmail())->toBe('test@example.com');
});

test('returns null when contact is not found', function () {
    $this->contactsApi->shouldReceive('getContactInfo')
        ->once()
        ->with('invalid@example.com')
        ->andThrow(new ApiException('Not found', 404));

    Log::shouldReceive('warning')->once();

    $result = $this->brevo->getContactInfo('invalid@example.com');

    expect($result)->toBeNull();
});

test('subscribes new contact with attributes', function () {
    $email = 'new@example.com';
    $listId = 'list-123';
    $attributes = ['name' => 'John Doe'];

    // Mock the initial contact check (contact doesn't exist)
    $this->contactsApi->shouldReceive('getContactInfo')
        ->once()
        ->with($email)
        ->andThrow(new ApiException('Not found', 404));

    // Mock the contact creation
    $this->contactsApi->shouldReceive('createContact')
        ->once()
        ->with(Mockery::on(function (CreateContact $contact) use ($email, $attributes) {
            return $contact->getEmail() === $email &&
                $contact->getAttributes() == (object) $attributes;
        }));

    // Mock the list subscription
    $this->contactsApi->shouldReceive('addContactToList')
        ->once()
        ->with($listId, Mockery::on(function (AddContactToList $request) use ($email) {
            return $request->getEmails() === [$email];
        }));

    // Ensure updateContact is never called for a new contact
    $this->contactsApi->shouldReceive('updateContact')
        ->never();

    $result = $this->brevo->subscribe($email, $listId, $attributes);

    expect($result)->toBeTrue();
});

test('updates existing contact and subscribes', function () {
    $email = 'existing@example.com';
    $listId = 'list-456';
    $attributes = ['name' => 'Updated Name'];

    // Mock the initial contact check (contact exists)
    $this->contactsApi->shouldReceive('getContactInfo')
        ->once()
        ->with($email)
        ->andReturn(new GetExtendedContactDetails);

    // Mock the contact update
    $this->contactsApi->shouldReceive('updateContact')
        ->once()
        ->with($email, Mockery::on(function (UpdateContact $contact) use ($attributes) {
            return $contact->getAttributes() == (object) $attributes;
        }));

    // Mock the list subscription
    $this->contactsApi->shouldReceive('addContactToList')
        ->once()
        ->with($listId, Mockery::on(function (AddContactToList $request) use ($email) {
            return $request->getEmails() === [$email];
        }));

    // Ensure createContact is never called for an existing contact
    $this->contactsApi->shouldReceive('createContact')
        ->never();

    $result = $this->brevo->subscribe($email, $listId, $attributes);

    expect($result)->toBeTrue();
});

test('handles subscription errors gracefully', function () {
    $this->contactsApi->shouldReceive('getContactInfo')
        ->andThrow(new ApiException('Server error', 500));

    Log::shouldReceive('error')->once();

    $result = $this->brevo->subscribe('error@example.com', 'list-123');

    expect($result)->toBeFalse();
});

test('unsubscribes contact successfully', function () {
    $email = 'user@example.com';
    $listId = 'list-789';

    $this->contactsApi->shouldReceive('removeContactFromList')
        ->once()
        ->with($listId, Mockery::on(function (RemoveContactFromList $request) use ($email) {
            return $request->getEmails() === [$email];
        }));

    $result = $this->brevo->unsubscribe($email, $listId);

    expect($result)->toBeTrue();
});

test('handles non-existent contact in unsubscribe as success', function () {
    $this->contactsApi->shouldReceive('removeContactFromList')
        ->andThrow(new ApiException('Not found', 404));

    Log::shouldReceive('warning')->once();

    $result = $this->brevo->unsubscribe('missing@example.com', 'list-123');

    expect($result)->toBeTrue();
});

test('handles unsubscribe errors appropriately', function () {
    $this->contactsApi->shouldReceive('removeContactFromList')
        ->andThrow(new ApiException('Server error', 500));

    Log::shouldReceive('error')->once();

    $result = $this->brevo->unsubscribe('error@example.com', 'list-123');

    expect($result)->toBeFalse();
});

afterEach(function () {
    Mockery::close();
});
