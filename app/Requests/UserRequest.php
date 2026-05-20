<?php

declare(strict_types=1);

namespace App\Requests;

/**
 * Class UserRequest
 *
 * Responsibility:
 * Validates user input data for registration and profile management.
 */
class UserRequest
{
    /**
     * Validation errors.
     *
     * @var array<string,string>
     */
    protected array $errors = [];

    /**
     * Form data.
     *
     * @var array<string,mixed>
     */
    protected array $data = [];

    /**
     * UserRequest constructor.
     *
     * @param array<string,mixed> $data Request payload
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate user data for create/update actions.
     *
     * @param bool $isUpdate Whether validation is for update action
     * @return bool
     */
    public function validate(bool $isUpdate = false): bool
    {
        $this->errors = [];

        $firstName = trim((string)($this->data['first_name'] ?? ''));
        $email = trim((string)($this->data['email'] ?? ''));
        $password = (string)($this->data['password'] ?? '');
        $roleId = $this->data['role_id'] ?? null;

        // First Name validation
        if ($firstName === '') {
            $this->errors['first_name'] = 'First name is required.';
        } elseif (mb_strlen($firstName) > 100) {
            $this->errors['first_name'] = 'First name cannot exceed 100 characters.';
        }

        // Email validation
        if ($email === '') {
            $this->errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Please enter a valid email address.';
        }

        // Password validation (Required only on Create, or if not empty on Update)
        if (!$isUpdate && $password === '') {
            $this->errors['password'] = 'Password is required.';
        } elseif ($password !== '' && strlen($password) < 8) {
            $this->errors['password'] = 'Password must be at least 8 characters long.';
        }

        // Role ID validation
        if ($roleId === null || $roleId === '' || (int)$roleId <= 0) {
            $this->errors['role_id'] = 'Please select a valid user role.';
        }

        return empty($this->errors);
    }

    /**
     * Returns sanitized data for database insertion/update.
     *
     * @return array<string,mixed>
     */
    public function sanitized(): array
    {
        return [
            'first_name'         => htmlspecialchars(trim((string)($this->data['first_name'] ?? ''))),
            'last_name'          => htmlspecialchars(trim((string)($this->data['last_name'] ?? ''))),
            'email'              => filter_var(trim((string)($this->data['email'] ?? '')), FILTER_SANITIZE_EMAIL),
            'phone_number'       => htmlspecialchars(trim((string)($this->data['phone_number'] ?? ''))),
            'role_id'            => (int)($this->data['role_id'] ?? 0),
            'status'             => (int)($this->data['status'] ?? 1),
            'can_login'          => (int)($this->data['can_login'] ?? 1),
            'is_locked_by_admin' => (int)($this->data['is_locked_by_admin'] ?? 0),
        ];
    }

    /**
     * Get validation errors.
     *
     * @return array<string,string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}