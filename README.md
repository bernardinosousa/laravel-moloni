# Laravel Moloni

Integration with [Moloni](https://www.moloni.pt/)

## Installation

You can install the package via composer:

```bash
composer require tiagosimoesdev/laravel-moloni
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="moloni-config"
```

This is the contents of the published config file:

```php
return [
    'client_secret'   =>  env('MOLONI_CLIENT_SECRET', null),
    'client_id'   =>  env('MOLONI_DEVELOPER_ID', null),
    'username'   =>  env('MOLONI_USERNAME', null),
    'password'   =>  env('MOLONI_PASSWORD', null),
    'company_id'    =>  env('MOLONI_COMPANY_ID', null),
    'sandbox'       => env('MOLONI_SANDBOX', true)
];
```

## Usage

```php
$variable = new Moloni();
echo $variable->echoPhrase('Hello, VendorName!');
```

## Credits

- [Tiago Simoes](https://github.com/tiagosimoesdev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
