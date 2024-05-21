# Installation

You can install the package via composer:

```bash
composer require stitch-digital/laravel-simpro
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-simpro-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-simpro-config"
```

This is the contents of the published config file:

```php
return [
];
```
