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


