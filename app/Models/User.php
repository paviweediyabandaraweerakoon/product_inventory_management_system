<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class User
 * Handles all database operations related to the user entity.
 * * @package App\Models
 */
class User extends Model
{
    /**
     * @var string Table name for this model
     */
    protected string $table = 'users';

    /**
     * Finds a user by their email address.
     * Only returns users where deleted_at is NULL (Soft delete check).
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email): array|false
    {
        // Supervisor Feedback: Ensure deleted_at IS NULL is checked
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates failed login attempts and sets lockout time.
     *
     * @param int $id
     * @param int $attempts
     * @param string|null $lockUntil
     * @return bool
     */
    public function updateLoginAttempts(int $id, int $attempts, ?string $lockUntil = null): bool
    {
        $sql = "UPDATE {$this->table} 
                SET wrong_attempts = :wa, lock_until = :lu 
                WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'wa' => $attempts,
            'lu' => $lockUntil,
            'id' => $id
        ]);
    }

    /**
     * Resets failed login attempts for a user.
     *
     * @param int $id
     * @return bool
     */
    public function resetAttempts(int $id): bool
    {
        $sql = "UPDATE {$this->table} 
                SET wrong_attempts = 0, lock_until = NULL 
                WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Creates a new user record in the database.
     * Sanitizes name input and handles verification token.
     *
     * @param array $data Contains full_name, email, password_hash, verification_token
     * @return bool
     */
    public function createUser(array $data): bool
    {
        // Sanitize name split logic
        $name = trim((string)$data['full_name']);
        $parts = explode(' ', $name, 2);
        $first = $parts[0];
        $last = $parts[1] ?? '';

        $sql = "INSERT INTO {$this->table}
                (first_name, last_name, email, password_hash, role_id, status, verification_token, created_at)
                VALUES(:first, :last, :email, :pass, :role, :status, :token, NOW())";

        $stmt = $this->db->prepare($sql);

        // Role 2 = User, Status 0 = Pending/Unverified
        return $stmt->execute([
            'first'  => htmlspecialchars($first),
            'last'   => htmlspecialchars($last),
            'email'  => htmlspecialchars($data['email']),
            'pass'   => $data['password_hash'],
            'role'   => 2,
            'status' => 0,
            'token'  => $data['verification_token']
        ]);
    }

    /**
     * Verifies user email via token and activates the account.
     *
     * @param string $token
     * @return bool
     */
    public function verifyEmail(string $token): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = 1, email_verified_at = NOW(), verification_token = NULL
                WHERE verification_token = :token AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['token' => $token]);
    }
}