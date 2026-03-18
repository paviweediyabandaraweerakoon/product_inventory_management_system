<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * Sanitize input array for safe display and processing.
     * * @param array $input
     * @return array
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
     * Returns a default '-' for the last name if not provided.
     * * @param string $fullName
     * @return array
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
     * Generate a secure 6-digit numeric OTP.
     * * @return string
     */
    public static function generateOtp(): string
    {
        try {
            return (string) random_int(100000, 999999);
        } catch (\Exception $e) {
            // Fallback for environments with low entropy
            return str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Generate or retrieve a CSRF token from the session.
     * * @param bool $refresh Force generation of a new token.
     * @return string
     */
    public static function generateCsrfToken(bool $refresh = false): string
    {
        if (empty($_SESSION['csrf_token']) || $refresh) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate a CSRF token against the session store.
     * * @param string|null $token
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        // Use hash_equals to prevent timing attacks
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Comprehensive password strength validator.
     * Minimum 8 characters, at least one uppercase, one lowercase, one number, and one special character.
     * * @param string $password
     * @return bool
     */
    public static function isStrongPassword(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password) === 1;
    }
}