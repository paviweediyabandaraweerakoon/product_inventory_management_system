<?php

namespace App\Services;

use App\Models\User;
use App\Core\Env;
use App\Helpers\SecurityHelper;
use App\Helpers\MailHelper;
use Throwable;

/**
 * Class AuthService
 * * Handles core business logic for user authentication, registration, 
 * and login attempt management.
 */
class AuthService
{
    /** @var User The user data access model. */
    private User $userModel;

    /** @var NotificationService Service for sending emails and notifications. */
    private NotificationService $notificationService;

    /** @var int Maximum allowed failed login attempts before lockout. */
    private int $maxAttempts;

    /** @var int Duration of account lockout in minutes. */
    private int $lockMinutes;

    /** @var int Validity period of OTP in minutes. */
    private int $otpExpiryMinutes;

    /**
     * AuthService constructor.
     * Initializes dependencies and loads configuration from environment variables.
     */
    public function __construct()
    {
        $this->userModel = new User();
        $this->notificationService = new NotificationService();
        $this->maxAttempts = (int)Env::get('AUTH_MAX_ATTEMPTS', 3);
        $this->lockMinutes = (int)Env::get('AUTH_LOCK_MINUTES', 15);
        $this->otpExpiryMinutes = (int)Env::get('AUTH_OTP_EXPIRY_MINUTES', 10);
    }

    /**
     * Authenticates a user using email and password.
     * * @param string $email
     * @param string $password
     * @return array Result containing status, and either user data or error/redirect details.
     */
    public function authenticate(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);

        // Verify credentials
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return [
                'success' => false, 
                'error'   => $this->handleFailedLogin($user), 
                'user'    => $user
            ];
        }

        // Check if email is verified
        if (!$this->userModel->isEmailVerified($user)) {
            return [
                'success'  => false, 
                'redirect' => '/verify-otp?email=' . urlencode($email)
            ];
        }

        // Reset attempts on successful login
        $this->userModel->resetLoginAttempts((int)$user['id']);
        
        return [
            'success' => true, 
            'user'    => $user
        ];
    }

    /**
     * Handles the creation of a new user account and triggers verification email.
     * * @param array $data Sanitized registration data.
     * @return bool True if registration was successful, false otherwise.
     */
    public function registerUser(array $data): bool
    {
        [$firstName, $lastName] = SecurityHelper::splitName($data['full_name']);
        $otp = SecurityHelper::generateOtp();
        $otpExpiresAt = date('Y-m-d H:i:s', strtotime("+{$this->otpExpiryMinutes} minutes"));

        $userId = $this->userModel->createUser([
            'first_name'                        => $firstName,
            'last_name'                         => $lastName,
            'email'                             => $data['email'],
            'password_hash'                     => password_hash($data['password'], PASSWORD_BCRYPT),
            'role_id'                           => (int)Env::get('AUTH_DEFAULT_STAFF_ROLE_ID', 3),
            'status'                            => 1,
            'can_login'                         => 1,
            'wrong_attempts'                    => 0,
            'lockout_duration'                  => $this->lockMinutes,
            'email_verification_otp'            => $otp,
            'email_verification_otp_expires_at' => $otpExpiresAt,
        ]);

        if ($userId) {
            $this->notificationService->sendVerificationOtpEmail(
                $data['email'], 
                $firstName, 
                $otp, 
                $this->otpExpiryMinutes
            );
            return true;
        }

        return false;
    }

    /**
     * Manages failed login attempts, updates the lockout status, and sends security alerts.
     * * @param array|null $user The user record, if found.
     * @return string Error message to be displayed to the user.
     */
    private function handleFailedLogin(?array $user): string
    {
        if (!$user) {
            return "Invalid credentials.";
        }

        $attempts = ($user['wrong_attempts'] ?? 0) + 1;
        $lockUntil = $attempts >= $this->maxAttempts 
            ? date('Y-m-d H:i:s', strtotime("+{$this->lockMinutes} minutes")) 
            : null;

        $this->userModel->updateLoginAttempts((int)$user['id'], $attempts, $lockUntil);

        if ($lockUntil) {
            MailHelper::securityAlert($user['email']);
            return "Account locked for {$this->lockMinutes} mins due to multiple failed attempts.";
        }

        $remaining = max(0, $this->maxAttempts - $attempts);
        return "Invalid credentials. You have {$remaining} attempts left.";
    }
}