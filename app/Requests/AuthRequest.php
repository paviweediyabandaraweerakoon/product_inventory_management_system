<?php
namespace App\Request;

// Class AuthRequest
// Handles validation and sanitization of authentication-related requests (login, register).

class AuthRequest
{
    private array $data;
    private array $errors = [];

    public function __construct(array $postData)
    {
        session_start();
        $this->data = $postData;
        $this->verifyCsrf();
    }

    private function verifyCsrf(): void
    {
        $token = $this->data['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            die('Invalid CSRF token.');
        }
    }

    public function validateLogin(): array
    {
        $email = trim($this->data['email'] ?? '');
        $password = trim($this->data['password'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email address.';
        }

        if (empty($password)) {
            $this->errors['password'] = 'Password cannot be empty.';
        }

        return $this->errors;
    }

    
    /**
     * Validates registration input data and returns an array of errors if any.
     *
     * @return array Associative array of field errors (e.g., ['email' => 'Invalid email'])
     */

    public function validateRegister(): array
    {
        $fullName = trim($this->data['full_name'] ?? '');
        $email = trim($this->data['email'] ?? '');
        $password = trim($this->data['password'] ?? '');
        $confirmPassword = trim($this->data['confirm_password'] ?? '');
        $phone = trim($this->data['phone_number'] ?? '');

        if (!$fullName) $this->errors['full_name'] = 'Full name required.';
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $this->errors['email'] = 'Invalid email.';
        if (!$password || strlen($password) < 8) $this->errors['password'] = 'Password must be 8+ chars.';
        if ($password !== $confirmPassword) $this->errors['confirm_password'] = 'Passwords do not match.';
        if ($phone && !preg_match('/^\+?\d{9,15}$/', $phone)) $this->errors['phone_number'] = 'Invalid phone number.';

        return $this->errors;
    }

    /**
     * Returns the sanitized input data.
     *
     * @return array
     */
    
    public function sanitized(): array
    {
        return [
            'full_name' => htmlspecialchars($this->data['full_name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($this->data['email'] ?? '', ENT_QUOTES, 'UTF-8'),
            'password' => $this->data['password'] ?? '',
            'phone_number' => htmlspecialchars($this->data['phone_number'] ?? '', ENT_QUOTES, 'UTF-8'),
        ];
    }
}