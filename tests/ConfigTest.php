<?php


	it('has a brevo config file', function () {
		// Assert that the config file exists and is an array
		expect(config('brevo'))->toBeArray();
	});

	it('has the correct config keys', function () {
		// Assert that the config file contains the expected keys
		expect(config('brevo'))->toHaveKeys(['api_key', 'default_list_id']);
	});
