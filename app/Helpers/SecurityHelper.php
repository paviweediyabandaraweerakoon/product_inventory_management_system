<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;

/**
 * Reusable security-related helper methods.
 */
class SecurityHelper
{
    /**
     * Generate a 6-digit OTP.
     *
     * @return string
     */
    public static function generateOtp(): string
    {
        try {
            return (string) random_int(100000, 999999);
        } catch (Exception $e) {
            error_log(
                date('Y-m-d H:i:s') .
                ' > SecurityHelper > generateOtp fallback: ' .
                $e->getMessage()
            );

            return str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Validate password strength.
     *
     * Minimum 8 characters, at least one uppercase,
     * one lowercase, one number, and one special character.
     *
     * @param string $password
     * @return bool
     */
    public static function isValidPassword(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password) === 1;
    }

    /**
     * Generate or refresh CSRF token.
     *
     * @param bool $refresh
     * @return string
     */
    public static function generateCsrfToken(bool $refresh = false): string
    {
        if (empty($_SESSION['csrf_token']) || $refresh) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                error_log(
                    date('Y-m-d H:i:s') .
                    ' > SecurityHelper > CSRF generation fallback: ' .
                    $e->getMessage()
                );

                $_SESSION['csrf_token'] = md5(uniqid((string) mt_rand(), true));
            }
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token.
     *
     * @param string|null $token
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool
    {
        return !empty($_SESSION['csrf_token'])
            && !empty($token)
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}