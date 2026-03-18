<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * Sanitize input array for safe display / processing.
     */
    public static function sanitize(array $input): array
    {
        return array_map(
            fn($value) => htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8'),
            $input
        );
    }

    /**
     * Split full name into first and last names.
     */
    public static function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        return [
            $parts[0] ?? '',
            count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '-'
        ];
    }

    /**
     * Generate a secure 6-digit OTP code.
     */
    public static function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * Generate CSRF token and store in session.
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token from POST request.
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || !$token) {
            return false;
        }

        $isValid = hash_equals($_SESSION['csrf_token'], $token);

        // Optional: one-time use
        unset($_SESSION['csrf_token']);

        return $isValid;
    }

    /**
     * Strong password validator
     */
    public static function isStrongPassword(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password) === 1;
    }
}