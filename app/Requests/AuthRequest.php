<?php

declare(strict_types=1);

namespace App\Requests;

use App\Helpers\SecurityHelper;

/**
 * Handles validation and normalization of authentication-related request data.
 */
class AuthRequest
{
    private array $data;

    /**
     * @var array<string, string>
     */
    private array $errors = [];

    /**
     * @param array<string, mixed> $postData
     */
    public function __construct(array $postData)
    {
        $this->data = $postData;
    }

    /**
     * Validate login form input.
     *
     * @return array<string, string>
     */
    public function validateLogin(): array
    {
        $this->errors = [];

        $email = trim((string) ($this->data['email'] ?? ''));
        $password = (string) ($this->data['password'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'A valid email address is required.';
        }

        if ($password === '') {
            $this->errors['password'] = 'Password cannot be empty.';
        }

        return $this->errors;
    }

    /**
     * Validate registration form input.
     *
     * @return array<string, string>
     */
    public function validateRegister(): array
    {
        $this->errors = [];

        $fullName = trim((string) ($this->data['full_name'] ?? ''));
        $email = trim((string) ($this->data['email'] ?? ''));
        $phone = trim((string) ($this->data['phone_number'] ?? ''));
        $password = (string) ($this->data['password'] ?? '');
        $confirmPassword = (string) ($this->data['confirm_password'] ?? '');

        if ($fullName === '') {
            $this->errors['full_name'] = 'Full name is required.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'A valid email address is required.';
        }

        if (!SecurityHelper::isValidPassword($password)) {
            $this->errors['password'] = 'Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.';
        }

        if ($confirmPassword === '') {
            $this->errors['confirm_password'] = 'Please confirm your password.';
        } elseif ($password !== $confirmPassword) {
            $this->errors['confirm_password'] = 'Password confirmation does not match.';
        }

        if ($phone !== '' && !preg_match('/^\+?\d{9,15}$/', $phone)) {
            $this->errors['phone_number'] = 'Phone number must be 9 to 15 digits and may start with +.';
        }

        return $this->errors;
    }

    /**
     * Return normalized and safe auth data.
     *
     * Important:
     * - Password remains raw (unmodified) for authentication/hashing.
     * - Output escaping should happen at the view layer, not here.
     *
     * @return array{
     *     first_name: string,
     *     last_name: string,
     *     email: string,
     *     password: string,
     *     phone_number: string|null
     * }
     */
    public function sanitized(): array
    {
        $fullName = trim((string) ($this->data['full_name'] ?? ''));
        $nameParts = preg_split('/\s+/', $fullName, 2) ?: [];

        $firstName = trim($nameParts[0] ?? '');
        $lastName = trim($nameParts[1] ?? '-');

        $phone = preg_replace('/[^\d+]/', '', trim((string) ($this->data['phone_number'] ?? '')));
        $phone = $phone !== '' ? $phone : null;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower(trim((string) ($this->data['email'] ?? ''))),
            'password' => (string) ($this->data['password'] ?? ''),
            'phone_number' => $phone,
        ];
    }
}