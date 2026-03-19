<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Handles user-related database access.
 */
class User extends Model
{
    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array<string, mixed>|false
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT id, first_name, last_name, email, phone_number,
                       password_hash, role_id, status, can_login,
                       wrong_attempts, is_locked_by_admin,
                       email_verification_otp, email_verification_otp_expires_at,
                       email_verified_at
                FROM users
                WHERE email = :email
                  AND deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->query($sql, ['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user.
     *
     * @param array{
     *     first_name: string,
     *     last_name: string,
     *     email: string,
     *     phone_number: string|null,
     *     password_hash: string,
     *     role_id: int,
     *     status: int,
     *     can_login: int,
     *     wrong_attempts: int,
     *     email_verification_otp: string|null,
     *     email_verification_otp_expires_at: string|null
     * } $data
     * @return int
     */
    public function createUser(array $data): int
    {
        $sql = "INSERT INTO users (
                    first_name,
                    last_name,
                    email,
                    phone_number,
                    password_hash,
                    role_id,
                    status,
                    can_login,
                    wrong_attempts,
                    email_verification_otp,
                    email_verification_otp_expires_at,
                    created_at,
                    updated_at
                ) VALUES (
                    :first_name,
                    :last_name,
                    :email,
                    :phone_number,
                    :password_hash,
                    :role_id,
                    :status,
                    :can_login,
                    :wrong_attempts,
                    :email_verification_otp,
                    :email_verification_otp_expires_at,
                    NOW(),
                    NOW()
                )";

        $this->query($sql, [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password_hash' => $data['password_hash'],
            'role_id' => $data['role_id'],
            'status' => $data['status'],
            'can_login' => $data['can_login'],
            'wrong_attempts' => $data['wrong_attempts'],
            'email_verification_otp' => $data['email_verification_otp'],
            'email_verification_otp_expires_at' => $data['email_verification_otp_expires_at'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Reset login attempts after successful login.
     *
     * @param int $userId
     * @return bool
     */
    public function resetLoginAttempts(int $userId): bool
    {
        $sql = "UPDATE users
                SET wrong_attempts = 0,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, ['id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Update login attempts after failed login.
     *
     * @param int $userId
     * @param int $attempts
     * @return bool
     */
    public function updateLoginAttempts(int $userId, int $attempts): bool
    {
        $sql = "UPDATE users
                SET wrong_attempts = :attempts,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, [
            'attempts' => $attempts,
            'id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Mark a user's email as verified.
     *
     * @param int $userId
     * @return bool
     */
    public function markEmailAsVerified(int $userId): bool
    {
        $sql = "UPDATE users
                SET email_verified_at = NOW(),
                    email_verification_otp = NULL,
                    email_verification_otp_expires_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, ['id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Update a user's verification OTP.
     *
     * @param int $userId
     * @param string $otp
     * @param string $expiresAt
     * @return bool
     */
    public function updateVerificationOtp(int $userId, string $otp, string $expiresAt): bool
    {
        $sql = "UPDATE users
                SET email_verification_otp = :otp,
                    email_verification_otp_expires_at = :expires_at,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, [
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }
}