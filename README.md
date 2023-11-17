# Vesta Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/docchula/vesta-client.svg?style=flat-square)](https://packagist.org/packages/docchula/vesta-client)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/docchula/vesta-client/run-tests?label=tests)](https://github.com/docchula/vesta-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/docchula/vesta-client/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/docchula/vesta-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/docchula/vesta-client.svg?style=flat-square)](https://packagist.org/packages/docchula/vesta-client)

Laravel Client Service for Vesta, the MDCU Directory Provider

## Installation

You can install the package via composer:

```bash
composer require docchula/vesta-client
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="vesta-client-config"
```

You should set the following ENV variables:

- `VESTA_SECRET` - The secret key provided by Vesta
- `VESTA_ISSUER` - The issuer URL provided by Vesta
- `VESTA_URL`- The URL of the Vesta API (optional)

## Usage

```php
$vestaClient = new Docchula\VestaClient();
// or use Laravel's Facade:
$response = VestaClient::retrieveStudent($identifier, $userEmail, ['title', 'first_name', 'last_name', 'first_name_en', 'last_name_en', 'email', 'nickname']);
if ($response->successful()) {
    $data = $response->json();
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Siwat Techavoranant](https://github.com/keenthekeen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
