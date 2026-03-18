<?php

namespace App\Request;

/**
 * Class AuthRequest
 * Handles validation and sanitization of authentication-related input data.
 */
class AuthRequest
{
    /** @var array The raw input data from the request. */
    private array $data;

    /** @var array Storage for validation error messages. */
    private array $errors = [];

    /**
     * AuthRequest constructor.
     * @param array $postData The $_POST data or equivalent input array.
     */
    public function __construct(array $postData)
    {
        $this->data = $postData;
    }

    /**
     * Validates login input credentials.
     * @return array Returns an associative array of errors, empty if valid.
     */
    public function validateLogin(): array
    {
        $email = trim($this->data['email'] ?? '');
        $password = trim($this->data['password'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'A valid email address is required.';
        }

        if (empty($password)) {
            $this->errors['password'] = 'Password cannot be empty.';
        }

        return $this->errors;
    }

    /**
     * Validates registration input data with detailed error messages.
     * @return array Returns an associative array of errors.
     */
    public function validateRegister(): array
    {
        $fullName = trim($this->data['full_name'] ?? '');
        $email = trim($this->data['email'] ?? '');
        $password = trim($this->data['password'] ?? '');
        $confirmPassword = trim($this->data['confirm_password'] ?? '');
        $phone = trim($this->data['phone_number'] ?? '');

        if (empty($fullName)) {
            $this->errors['full_name'] = 'Full name is required to create an account.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Please provide a valid corporate or personal email.';
        }

        if (empty($password) || strlen($password) < 8) {
            $this->errors['password'] = 'Password must be at least 8 characters for better security.';
        }

        if ($password !== $confirmPassword) {
            $this->errors['confirm_password'] = 'The confirmation password does not match.';
        }

        if (!empty($phone) && !preg_match('/^\+?\d{9,15}$/', $phone)) {
            $this->errors['phone_number'] = 'The phone number format is invalid.';
        }

        return $this->errors;
    }

    /**
     * Returns the sanitized and filtered input data.
     * @return array
     */
    public function sanitized(): array
    {
        return [
            'full_name'    => htmlspecialchars(trim($this->data['full_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'email'        => filter_var(trim($this->data['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'password'     => $this->data['password'] ?? '', 
            'phone_number' => htmlspecialchars(trim($this->data['phone_number'] ?? ''), ENT_QUOTES, 'UTF-8'),
        ];
    }
}