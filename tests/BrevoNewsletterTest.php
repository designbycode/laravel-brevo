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


afterEach(function () {
    Mockery::close();
});
