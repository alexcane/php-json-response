![Tests](https://github.com/alexcane/php-json-response/actions/workflows/phpunit.yml/badge.svg)
![Build Status](https://github.com/alexcane/php-json-response/actions/workflows/unit_test.yml/badge.svg)
[![License](https://poser.pugx.org/alexcane/php-json-response/license)](https://packagist.org/packages/alexcane/php-json-response)


# PHPJsonResponse

---
Personal PHP Class Library for format json response
---

This is a personal library to standardise my json response on all my projects.

### Response structure
- `status` : string 'error' or 'success'.
- `error_msg` : array of errors message.
- `data` : array of data provided.
- `response` : mixed value is possible like object, Class or HTML string, etc.

### How to use ?
1) Instance the class
```php
$resp = $new JsonResp(/* array of data */);
```
2) Check if your data is valid and add your error message.
```php
if (0 <= $data['number']) $resp->addErrMsg('number must be more than 0');
```
3) Before return response you can check if there are errors.
```php
if ($resp->isError()) die $resp->returnResponse(true);
```
4) 2 ways to return response : JSON string or Array.
```php
$resp->returnResponse(true); // JSON string
$resp->returnResponse(); // Array
```
