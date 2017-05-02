[![Packagist](https://img.shields.io/packagist/v/jaimeeee/laravelpanel.svg)](https://packagist.org/packages/jaimeeee/laravelpanel) [![Packagist](https://img.shields.io/packagist/dt/jaimeeee/laravelpanel.svg)](https://packagist.org/packages/jaimeeee/laravelpanel)

# laravelpanel
A Panel for Laravel Websites that provides an easy way to create, edit and delete new objects in your database, with almost no effort in configuration files.

## Requirements

- PHP >=5.5.9
- Symfony/Yaml >=3.1
- Intervention/Image >=2.3
- Laravel's authentification

## Instalation

Require this package with Composer:

```shell
composer require jaimeeee/laravelpanel
```

After updating Composer, add the Service Provider to the providers array in `config/app.php`:

```php
Jaimeeee\Panel\PanelServiceProvider::class,
```

If you haven't enabled Laravel's authentification make sure to run the following command:

```shell
php artisan make:auth
```

This will create the necesarry controllers and views to log in.

### Copy necesarry files to your folders

To copy all the files to your folders and edit them you just need to run the following command:

```shell
php artisan vendor:publish --provider="Jaimeeee\Panel\PanelServiceProvider"
```

This will copy the config file, an example blueprint, and the stylesheet.

## Blueprints

The blueprints are Yaml files located in `config/panel/` and they represent each entity of your panel.

This is an example blueprint:

```yaml
class: App\User
icon: fa fa-users
sort:
  field: name
  order: asc
list:
  id: ID
  name: Name
  email: E-Mail
fields:
  name:
    label: Name
    type: text
    placeholder: John Doe
    validate: required
  email:
    label: E-Mail
    type: text
    placeholder: email@somewebsite.com
    validate: required|email
```

