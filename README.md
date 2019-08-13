<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>


# Standard CRP System for Small Company

Migrated company internal ERP web application from Laravel 4.2 to Laravel 5.6. The new version get rid of deprecated packages and plugin. It reduces security risk and enhances overall performance. 

## Requirements
```
PHP >= 7.1.3
BCMath PHP Extension
Ctype PHP Extension
JSON PHP Extension
Mbstring PHP Extension
OpenSSL PHP Extension
PDO PHP Extension
Tokenizer PHP Extension
XML PHP Extension
```
### Prerequisites

First, download the Laravel installer using Composer:

```
composer global require laravel/installer
```

### Installing

Update your composer to the latest version and get the required packages for the project

```
composer update
```
```
composer install
```
Note: Edit `.env` and set your database connection details
(When installed via git clone or download, run `php artisan key:generate` and `php artisan jwt:secret`)

Migrate database
```php artisan migrate```

If you encounter any problems for installation, please click <a href="https://laravel.com/docs/5.8">this link</a>

## Running the tests

Explain how to run the automated tests for this system



## Deployment

```bash
# project live
php artisan serve
```
## Built With

* [Laravel](https://laravel.com/) - The web framework used
* [Bootstrap](https://getbootstrap.com/) - Front-end framework 
* [Axios](https://cnpmjs.org/package/axios) - Used to generate REST API



## Authors

* **Jacky Fan**

See also the list of [contributors](https://github.com/dlfjj/ERP_System/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* This repo does not contain any data
* Hat tip to anyone whose code was used
* Peace, Share, Love

