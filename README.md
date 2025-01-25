## Overview
[![Latest Version on Packagist](https://img.shields.io/packagist/v/designbycode/laravel-brevo.svg?style=flat-square)](https://packagist.org/packages/designbycode/laravel-brevo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/designbycode/laravel-brevo/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/designbycode/laravel-brevo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/designbycode/laravel-brevo/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/designbycode/laravel-brevo/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/designbycode/laravel-brevo.svg?style=flat-square)](https://packagist.org/packages/designbycode/laravel-brevo)

The LaravelBrevo package is a Laravel wrapper for integrating with the Brevo API (formerly Sendinblue). It simplifies interactions with Brevo's email marketing and contact management features, allowing you to manage contacts, subscribe/unsubscribe users, and retrieve contact information seamlessly within your Laravel application.

This version of the documentation demonstrates how to use the package via the Facade for cleaner and more expressive code.


## Use Cases
This package is ideal for:
1. Email Marketing:
   * Subscribe users to mailing lists.
   * Unsubscribe users from mailing lists.
   * Update user attributes (e.g., name, preferences).

2. Contact Management:
   * Retrieve contact details.
   * Create or update contacts in Brevo.

3. Automation:
   * Automatically add new users to Brevo lists during registration.
   * Sync user data between your application and Brevo.


## Support us



## Installation

You can install the package via composer:

```bash
composer require designbycode/laravel-brevo
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="brevo" 
```

Add your Brevo API key to the .env file:

```bash
BREVO_API_KEY=your-api-key
```

## Configuration
```php
return [
    'api_key' => env('BREVO_API_KEY', ''),
];
````
api_key: Your Brevo API key. This is required to authenticate API requests.



## Usage
### Retrieve Contact Information.

To retrieve details for a specific contact by email:
```php
use Designbycode\LaravelBrevo\Facades\Brevo;

if ($contact = Brevo::getContactInfo('test@example.com')) {
    echo "Contact Name: " . $contact->getAttributes()->name;
} else {
    echo "Contact not found.";
}
```
### Subscribe a Contact
To subscribe a contact to a mailing list:

```php
use Designbycode\LaravelBrevo\Facades\Brevo;
if ($success = Brevo::subscribe('test@example.com', $listId)) {
    echo "Contact subscribed successfully!";
} else {
    echo "Failed to subscribe contact.";
}
```

### Unsubscribe a Contact
To unsubscribe a contact from a mailing list:

```php
use Designbycode\LaravelBrevo\Facades\Brevo;

if ($success = Brevo::unsubscribe('test@example.com', $listId)) {
    echo "Contact unsubscribed successfully!";
} else {
    echo "Failed to unsubscribe contact.";
}
```

### Methods
`Brevo::getContactInfo(string $email): ?GetExtendedContactDetails`
* Retrieves contact details for the specified email.
* Returns `null` if the contact is not found.

`Brevo::subscribe(string $email, int $listId, array $attributes = []): bool`
* Subscribes a contact to a mailing list.
* Creates a new contact if they don't exist, or updates an existing contact.
* Returns `true` on success, `false` on failure.

`Brevo::unsubscribe(string $email, int $listId): bool`
* Unsubscribes a contact from a mailing list.
* Returns `true` on success, `false` on failure.

### Error Handling
The package handles API errors gracefully:
* 404 Not Found: Logs a warning and returns `null` or `false`.
* 500 Server Error: Logs an error and returns `false`.
* Other exceptions are logged and handled appropriately.











## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Claude Myburgh](https://github.com/claudemyburgh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
