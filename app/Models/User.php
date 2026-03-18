<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class User
 *
 * Handles all database operations related to users and authentication.
 */
class User extends Model
{
    /**
     * Find an active, non-deleted user by email.
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT *
                FROM users
                WHERE email = :email
                  AND deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->query($sql, ['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check whether an email is already registered.
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== false;
    }

    /**
     * Create a new user record.
     *
     * @param array $data
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
                    lockout_duration,
                    verification_token,
                    email_verification_otp,
                    email_verification_otp_expires_at
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
                    :lockout_duration,
                    :verification_token,
                    :email_verification_otp,
                    :email_verification_otp_expires_at
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
            'lockout_duration' => $data['lockout_duration'],
            'verification_token' => $data['verification_token'],
            'email_verification_otp' => $data['email_verification_otp'],
            'email_verification_otp_expires_at' => $data['email_verification_otp_expires_at'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update login attempts and temporary lock timestamp.
     *
     * @param int $userId
     * @param int $attempts
     * @param string|null $lockUntil
     * @return bool
     */
    public function updateLoginAttempts(int $userId, int $attempts, ?string $lockUntil): bool
    {
        $sql = "UPDATE users
                SET wrong_attempts = :wrong_attempts,
                    lock_until = :lock_until,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, [
            'wrong_attempts' => $attempts,
            'lock_until' => $lockUntil,
            'id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
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
                    lock_until = NULL,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, ['id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Mark a user's email as verified and clear OTP fields.
     *
     * @param int $userId
     * @return bool
     */
    public function markEmailAsVerified(int $userId): bool
    {
        $sql = "UPDATE users
                SET email_verified_at = NOW(),
                    verification_token = NULL,
                    email_verification_otp = NULL,
                    email_verification_otp_expires_at = NULL,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, ['id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Update email verification OTP and expiry.
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

    /**
     * Allow owner/admin to manually unlock a user account.
     *
     * @param int $userId
     * @return bool
     */
    public function unlockUser(int $userId): bool
    {
        $sql = "UPDATE users
                SET wrong_attempts = 0,
                    lock_until = NULL,
                    is_locked_by_admin = 0,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, ['id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Allow owner/admin to manually lock a user account.
     *
     * @param int $userId
     * @return bool
     */
    public function lockUserByAdmin(int $userId): bool
    {
        $sql = "UPDATE users
                SET is_locked_by_admin = 1,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, ['id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Allow owner/admin to enable or disable login permission.
     *
     * @param int $userId
     * @param bool $canLogin
     * @return bool
     */
    public function setLoginPermission(int $userId, bool $canLogin): bool
    {
        $sql = "UPDATE users
                SET can_login = :can_login,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, [
            'can_login' => $canLogin ? 1 : 0,
            'id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Allow owner/admin to change lockout duration per user.
     *
     * @param int $userId
     * @param int $minutes
     * @return bool
     */
    public function updateLockoutDuration(int $userId, int $minutes): bool
    {
        $sql = "UPDATE users
                SET lockout_duration = :lockout_duration,
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->query($sql, [
            'lockout_duration' => $minutes,
            'id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if a user is temporarily locked based on lock_until timestamp.
     *
     * @param array $user
     * @return bool
     */
    public function isTemporarilyLocked(array $user): bool
    {
        return !empty($user['lock_until']) && strtotime((string) $user['lock_until']) > time();
    }

    /**
     * Get remaining lock duration in minutes.
     *
     * @param string|null $lockUntil
     * @return int
     */
    public function getRemainingLockMinutes(?string $lockUntil): int
    {
        if (empty($lockUntil)) {
            return 0;
        }

        $remainingSeconds = strtotime($lockUntil) - time();

        return max(1, (int) ceil($remainingSeconds / 60));
    }

    /**
     * Check if login access is disabled.
     *
     * @param array $user
     * @return bool
     */
    public function isLoginDisabled(array $user): bool
    {
        return (int) ($user['can_login'] ?? 1) !== 1;
    }

    /**
     * Check if user account is admin locked.
     *
     * @param array $user
     * @return bool
     */
    public function isLockedByAdmin(array $user): bool
    {
        return (int) ($user['is_locked_by_admin'] ?? 0) === 1;
    }

    /**
     * Check if email is verified.
     *
     * @param array $user
     * @return bool
     */
    public function isEmailVerified(array $user): bool
    {
        return !empty($user['email_verified_at']);
    }

    /**
     * Check if user account is active.
     *
     * @param array $user
     * @return bool
     */
    public function isActive(array $user): bool
    {
        return (int) ($user['status'] ?? 1) === 1;
    }

    /**
     * Check whether provided OTP matches user record.
     *
     * @param array $user
     * @param string $otp
     * @return bool
     */
    public function isValidVerificationOtp(array $user, string $otp): bool
    {
        return !empty($user['email_verification_otp'])
            && $user['email_verification_otp'] === $otp;
    }

    /**
     * Check whether stored OTP is expired.
     *
     * @param array $user
     * @return bool
     */
    public function isVerificationOtpExpired(array $user): bool
    {
        if (empty($user['email_verification_otp_expires_at'])) {
            return true;
        }

        return strtotime((string) $user['email_verification_otp_expires_at']) < time();
    }
}