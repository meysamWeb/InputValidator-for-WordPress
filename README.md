# InputValidator for WordPress

## Overview

**InputValidator** is a robust PHP class designed to ensure the integrity and validity of user input in WordPress applications. By providing comprehensive validation solutions, it ensures that the data processed by your WordPress site adheres to expected formats and standards, thereby enhancing security and user experience.

## Features

- **Versatile Validation**: Supports a variety of input types including text, email, number, URL, checkbox, radio buttons, and more.
- **Custom Validations**: Implements custom validation methods for Iranian phone numbers, 6-digit confirmation codes, and password strength.
- **Easy Integration**: Simple to use within any WordPress plugin or theme.

## Installation

1. **Requirements**: Ensure your server is running PHP version 8.0 or above.
2. **Download**: Clone or download the repository.
3. **Include**: Include the `InputValidator.php` file in your WordPress plugin or theme.

## Usage

To use the `InputValidator` class, call the `validate` method with the appropriate parameters. Below is an example of how to integrate it into your code.

### Example

```php
<?php

namespace MeysamWeb;

class InputValidator {
	/**
	 * Validate required fields.
	 *
	 * @param array $fields The fields to validate.
	 * @param string $successMessage Custom success message.
	 * @return array An array with the validation result and message.
	 */
	public static function validate(array $fields, string $successMessage = 'All fields are valid.'): array {
		$errors = [];

		foreach ($fields as $field_name => $validation_info) {
			$validation_type = is_array($validation_info) ? $validation_info['type'] : $validation_info;
			$custom_messages = is_array($validation_info) && isset($validation_info['messages']) ? $validation_info['messages'] : [];

			$required_message = $custom_messages['required'] ?? 'The field ' . $field_name . ' is required.';
			$format_message = $custom_messages['format'] ?? 'The field ' . $field_name . ' is not in the correct format.';

			if (empty($_POST[$field_name])) {
				$errors[] = $required_message;
				continue;
			}

			switch ($validation_type) {
				case 'email':
					if (!is_email($_POST[$field_name])) {
						$errors[] = $format_message;
					}
					break;

				case 'number':
					if (!is_numeric($_POST[$field_name])) {
						$errors[] = $format_message;
					}
					break;

				case 'url':
					if (!filter_var($_POST[$field_name], FILTER_VALIDATE_URL)) {
						$errors[] = $format_message;
					}
					break;

				case 'checkbox':
					if (!in_array($_POST[$field_name], ['on', 'off', '1', '0', 'true', 'false'])) {
						$errors[] = $format_message;
					}
					break;

				case 'radio':
					if (!isset($validation_info['allowed_values']) ||
					    !in_array($_POST[$field_name], $validation_info['allowed_values'])) {
						$errors[] = $format_message;
					}
					break;

				case 'key':
					if (!ctype_alnum(str_replace(['-', '_'], '', $_POST[$field_name]))) {
						$errors[] = $format_message;
					}
					break;

				case 'textarea':
				case 'text':
					// For text and textarea, no additional validation needed beyond not empty
					break;

				case 'phone':
					if (!preg_match('/^(09\d{9}|(\+98|0098)9\d{9})$/', $_POST[$field_name])) {
						$errors[] = $format_message;
					}
					break;

				case 'digit_code':
					if (!preg_match('/^\d{6}$/', $_POST[$field_name])) {
						$errors[] = $format_message;
					}
					break;

				case 'password':
					$password = $_POST[$field_name];
					if (!self::validatePassword($password)) {
						$errors[] = $format_message;
					}
					break;

				default:
					$errors[] = 'Invalid validation type for field ' . $field_name . '.';
			}
		}

		if (!empty($errors)) {
			return [
				'success' => false,
				'message' => implode(' ', $errors),
			];
		}

		return [
			'success' => true,
			'message' => $successMessage,
		];
	}

	/**
	 * Validate password based on specific criteria.
	 *
	 * @param string $password The password to validate.
	 * @return bool True if the password meets all criteria, false otherwise.
	 */
	private static function validatePassword(string $password): bool {
		// Check if the password length is less than 8 characters
		if (strlen($password) < 8) {
			return false;
		}
		// Check if the password contains at least one digit
		if (!preg_match('/\d/', $password)) {
			return false;
		}
		// Check if the password contains at least one uppercase letter
		if (!preg_match('/[A-Z]/', $password)) {
			return false;
		}
		// Check if the password contains at least one lowercase letter
		if (!preg_match('/[a-z]/', $password)) {
			return false;
		}
		return true;
	}
}
```
## How to Use

Below is an example of how to use the `InputValidator` class in a typical WordPress form handling scenario.

#### Form Handling Example
```php
<?php

use MeysamWeb\InputValidator;

$validation_result = InputValidator::validate([
    'user_first_name' => 'text',
    'user_last_name'  => 'text',
    'user_email'      => 'email',
    'user_age'        => 'number',
    'user_website'    => 'url',
    'user_bio'        => 'textarea',
    'user_newsletter' => 'checkbox',
    'user_gender'     => [
        'type' => 'radio',
        'allowed_values' => ['male', 'female', 'other']
    ],
    'user_phone'      => [
        'type' => 'phone',
        'messages' => [
            'required' => 'Phone number is required.',
            'format' => 'Phone number must be a valid Iranian phone number.'
        ]
    ],
    'confirmation_code' => [
        'type' => 'digit_code',
        'messages' => [
            'required' => 'Confirmation code is required.',
            'format' => 'Confirmation code must be a 6-digit number.'
        ]
    ],
    'user_password'   => [
        'type' => 'password',
        'messages' => [
            'required' => 'Password is required.',
            'format' => 'Password must be at least 8 characters long and include a mix of letters and numbers.'
        ]
    ],
]);

if ( ! $validation_result['success'] ) {
	wp_send_json([
	'error'   => true,
	'message' => $validation_result['message'],
	], 403);
	die();
}

```

### Applicable Models

The `InputValidator` class can be effectively utilized in various models and scenarios within WordPress, such as:

* User Registration Forms: Validate user input during the registration process.
* Profile Update Forms: Ensure data integrity when users update their profile information.
* Contact Forms: Validate user input from contact forms to ensure it meets expected formats.
* Comment Forms: Validate comments to ensure they are in the correct format and prevent spam.

## Contributing

Contributions are welcome! If you have suggestions for improvements or have found a bug, please open an issue or submit a pull request. Make sure to follow the contributing guidelines.


## License
This project is licensed under the MIT License.

## Contact
For questions or support, please contact [MeysamWeb](https://github.com/meysamweb).