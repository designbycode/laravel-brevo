<?php

use Brevo\Client\Api\ContactsApi;
use Designbycode\LaravelBrevo\Brevo;

beforeEach(function () {
    $this->contactsApi = Mockery::mock(ContactsApi::class);
    $this->brevo = new Brevo($this->contactsApi);
});

afterEach(function () {
    Mockery::close();
});
