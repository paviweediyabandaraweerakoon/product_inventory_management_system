<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class User
 * Handles all database operations related to the user entity including
 * authentication data, account locking, and registration.
 */
class User extends Model
{
    /** @var string Table name for this model */
    protected string $table = 'users';

    /**
     * Finds a user by their email address.
     * Ensures only active (non-deleted) users are retrieved.
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL LIMIT 1";
        return $this->query($sql, [$email])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates failed login attempts and sets lockout time.
     */
    public function updateLoginAttempts(int $id, int $attempts, ?string $lockUntil = null): bool
    {
        $sql = "UPDATE {$this->table} 
                SET wrong_attempts = ?, lock_until = ? 
                WHERE id = ? AND deleted_at IS NULL";

        return $this->query($sql, [$attempts, $lockUntil, $id])->rowCount() > 0;
    }

    /**
     * Resets failed login attempts for a user after successful login.
     */
    public function resetAttempts(int $id): bool
    {
        $sql = "UPDATE {$this->table} 
                SET wrong_attempts = 0, lock_until = NULL 
                WHERE id = ? AND deleted_at IS NULL";

        return $this->query($sql, [$id])->rowCount() > 0;
    }

    /**
     * Registers a new user.
     * Sanitizes full name and maps it to first/last name fields.
     */
    public function create(array $data): string|false
    {
        // Name split logic (Professional handling)
        $fullName = trim((string)($data['full_name'] ?? ''));
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $insertData = [
            'first_name'         => htmlspecialchars($firstName),
            'last_name'          => htmlspecialchars($lastName),
            'email'              => htmlspecialchars($data['email']),
            'password_hash'      => $data['password_hash'],
            'role_id'            => 2, // Default: User
            'status'             => 0, // Default: Unverified
            'verification_token' => $data['verification_token'],
            'created_at'         => date('Y-m-d H:i:s')
        ];

        // Core Model එකේ තියෙන logic එකට අනුව column names ටික වෙන් කරගන්නවා
        $columns = implode(', ', array_keys($insertData));
        $placeholders = implode(', ', array_fill(0, count($insertData), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        
        $this->query($sql, array_values($insertData));
        
        return $this->db->lastInsertId();
    }

    /**
     * Verifies user email via token and activates the account.
     */
    public function verifyEmail(string $token): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = 1, email_verified_at = NOW(), verification_token = NULL
                WHERE verification_token = ? AND deleted_at IS NULL";

        return $this->query($sql, [$token])->rowCount() > 0;
    }
}