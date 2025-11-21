![Tests](https://github.com/alexcane/php-json-response/actions/workflows/ci.yml/badge.svg)
[![License](https://poser.pugx.org/alexcane/php-json-response/license)](https://packagist.org/packages/alexcane/php-json-response)

# PHPJsonResponse

Personal PHP Class Library for standardized JSON response formatting and password validation.

## Features

- ✅ Standardized JSON response structure
- ✅ Automatic data sanitization (trim strings, convert 'true'/'false' to booleans)
- ✅ Built-in password validation with preset security levels
- ✅ Multi-language support (French/English)
- ✅ Fully tested with PHPUnit

## Installation

```bash
composer require alexcane/php-json-response
```

## Usage

### JsonResp Class

The main class for handling JSON responses with consistent structure.

#### Basic Example

```php
use PhpJsonResp\JsonResp;

// Create instance with data
$resp = new JsonResp(['email' => '  user@example.com  ', 'active' => 'true']);

// Data is automatically sanitized
$data = $resp->getData();
// ['email' => 'user@example.com', 'active' => true]

// Add validation errors
if (empty($data['email'])) {
    $resp->addErrMsg('Email is required');
}

// Check for errors
if ($resp->isError()) {
    echo $resp->returnResponse(true); // Return as JSON string
    exit;
}

// Return successful response
echo $resp->returnResponse(true);
```

#### Response Structure

All responses follow this format:
```json
{
    "status": "success",
    "error_msg": [],
    "data": {
        "email": "user@example.com",
        "active": true
    }
}
```

On error:
```json
{
    "status": "error",
    "error_msg": ["Email is required", "Password is too short"],
    "data": {...}
}
```

#### Available Methods

```php
// Data management
$resp->setData(['key' => 'value']);
$resp->getData();
$resp->clearData();

// Error management
$resp->addErrMsg('Error message');
$resp->setErrMsg(['Error 1', 'Error 2']);
$resp->getErrMsg();
$resp->errorCount();

// Validation
$resp->isSuccess(); // true if no errors
$resp->isError();   // true if has errors

// Custom response field
$resp->setResponse('Custom data or HTML');
$resp->getResponse();

// Return response
$resp->returnResponse();      // Returns array
$resp->returnResponse(true);  // Returns JSON string
```

### PasswordValidator Class

Extension of JsonResp for password validation with configurable security levels.

#### Basic Example

```php
use PhpJsonResp\PasswordValidator;

// Create instance (default: light config, French messages)
$validator = new PasswordValidator(['password' => $_POST['password']]);

// Validate password
if (!$validator->isValidPassword($_POST['password'])) {
    echo $validator->returnResponse(true);
    exit;
}

// Password is valid
echo $validator->returnResponse(true);
```

#### Security Levels

**Light Config** (default)
```php
$validator->setLightConfig();
// - Min 6 characters
// - Requires digits
// - Uppercase optional
// - Symbols optional
```

**Medium Config**
```php
$validator->setMediumConfig();
// - Min 10 characters
// - Requires digits
// - Requires uppercase
// - Requires symbols
```

**Hard Config**
```php
$validator->setHardConfig();
// - Min 20 characters
// - Requires digits
// - Requires uppercase
// - Requires symbols
```

**Custom Config**
```php
$validator->setCustomConfig(
    min: 8,
    max: 50,
    uppercase: true,
    digits: true,
    symbols: false
);
```

#### Multi-language Support

```php
// French (default)
$validator = new PasswordValidator([], 'fr_FR');
// Error messages: "Mot de passe invalide", "6 caractères minimum", etc.

// English
$validator = new PasswordValidator([], 'en_US');
// Error messages: "Invalid Password", "at least 6 characters long", etc.
```

#### Get Generated Regex

```php
$validator->setMediumConfig();
$regex = $validator->getRegex();
// Returns: /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?:{}|<>-_])[A-Za-z\d!@#$%^&*(),.?:{}|<>-_]{10,100}$/
```

#### Complete Validation Example

```php
use PhpJsonResp\PasswordValidator;

$validator = new PasswordValidator($_POST, 'en_US');
$validator->setMediumConfig();

// Validate password
if (!$validator->isValidPassword($_POST['password'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo $validator->returnResponse(true);
    exit;
}

// Password is valid, continue with registration
$user = createUser($validator->getData());

$validator->setResponse(['user_id' => $user->id]);
header('Content-Type: application/json');
echo $validator->returnResponse(true);
```

## Response Structure

- `status`: string - 'error' or 'success'
- `error_msg`: array - List of error messages
- `data`: array - Validated/processed data (omitted if empty)
- `response`: mixed - Custom response data (omitted if empty)

## Requirements

- PHP ^7.4 || ^8.0
- ext-json

## Testing

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage
```

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Author

Alexandre Cane - [alexandre@linkidev.fr](mailto:alexandre@linkidev.fr)
