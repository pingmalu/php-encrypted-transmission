加密传输

## Requirement

PHP 5.4+ and PDO extension installed

## Get Started

### Install via composer

Add to composer.json configuration file.
```
$ composer require malu/php-encrypted-transmission
```

And update the composer
```
$ composer update
```

```php
// If you installed via composer, just use this code to require autoloader on the top of your projects.
require 'vendor/autoload.php';

// Using Medoo namespace
use Malu\Encrypted\Encrypted;

$data = ["hello","malu","bbq"];

// 加密输出
$encrypt_data = Encrypted::encrypt(json_encode($data), "34f7e6dd6acf03192d82f0337c8c54ba");
echo $encrypt_data;

// 解密输出
echo Encrypted::decrypt($encrypt_data, "34f7e6dd6acf03192d82f0337c8c54ba");

```
