<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Env;
use App\Helpers\SecurityHelper;
use App\Models\User;

/**
 * Handles authentication and registration business logic.
 */
class AuthService
{
    private User $userModel;
    private NotificationService $notificationService;
    private int $maxAttempts;
    private int $otpExpiryMinutes;

    public function __construct()
    {
        $this->userModel = new User();
        $this->notificationService = new NotificationService();
        $this->maxAttempts = (int) Env::get('AUTH_MAX_ATTEMPTS', 3);
        $this->otpExpiryMinutes = (int) Env::get('AUTH_OTP_EXPIRY_MINUTES', 10);
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email
     * @param string $password
     * @return array{
     *     success: bool,
     *     user?: array<string, mixed>|null,
     *     error: string|null,
     *     redirect: string|null
     * }
     */
    public function authenticate(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);

        if ($user === false) {
            return $this->failureResponse('Invalid email or password.');
        }

        if (!password_verify($password, (string) $user['password_hash'])) {
            $this->handleFailedLogin($user);

            return $this->failureResponse('Invalid email or password.');
        }

        if (isset($user['status']) && $user['status'] !== 'active') {
            return $this->failureResponse('Your account is deactivated. Please contact support.');
            }

        $accountValidation = $this->validateUserAccountState($user);
        if ($accountValidation !== null) {
            return $accountValidation;
        }

        $this->userModel->resetLoginAttempts((int) $user['id']);

        return $this->successResponse($user);
    }

    /**
     * Register a new user and send verification OTP.
     *
     * @param array{
     *     first_name: string,
     *     last_name: string,
     *     email: string,
     *     password: string,
     *     phone_number: string|null
     * } $data
     * @return array{
     *     success: bool,
     *     user?: array<string, mixed>|null,
     *     error: string|null,
     *     redirect: string|null
     * }
     */
    public function registerUser(array $data): array
    {
        if ($this->userModel->findByEmail($data['email']) !== false) {
            return $this->failureResponse('An account with this email already exists.');
        }

        $otp = SecurityHelper::generateOtp();
        $otpExpiresAt = date('Y-m-d H:i:s', strtotime("+{$this->otpExpiryMinutes} minutes"));

        $userId = $this->userModel->createUser([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id' => (int) Env::get('AUTH_DEFAULT_STAFF_ROLE_ID', 3),
            'status' => 1,
            'can_login' => 1,
            'wrong_attempts' => 0,
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => $otpExpiresAt,
        ]);

        if ($userId <= 0) {
            return $this->failureResponse('Failed to create account. Please try again.');
        }

        $emailSent = $this->notificationService->sendVerificationOtpEmail(
            $data['email'],
            $data['first_name'],
            $otp,
            $this->otpExpiryMinutes
        );

        if (!$emailSent) {
            error_log(
                date('Y-m-d H:i:s')
                . ' > AuthService > Failed to send verification OTP email to '
                . $data['email']
            );
        }

        return [
            'success' => true,
            'user' => null,
            'error' => null,
            'redirect' => '/verify-otp?email=' . urlencode($data['email']),
        ];
    }

    /**
     * Build a standard success response.
     *
     * @param array<string, mixed> $user
     * @return array{
     *     success: bool,
     *     user: array<string, mixed>,
     *     error: string|null,
     *     redirect: string|null
     * }
     */
    private function successResponse(array $user): array
    {
        return [
            'success' => true,
            'user' => $user,
            'error' => null,
            'redirect' => null,
        ];
    }

    /**
     * Build a standard failure response.
     *
     * @param string $error
     * @param string|null $redirect
     * @return array{
     *     success: bool,
     *     user: null,
     *     error: string,
     *     redirect: string|null
     * }
     */
    private function failureResponse(string $error, ?string $redirect = null): array
    {
        return [
            'success' => false,
            'user' => null,
            'error' => $error,
            'redirect' => $redirect,
        ];
    }

    /**
     * Handle failed login attempt count for an existing user.
     *
     * @param array<string, mixed> $user
     */
    private function handleFailedLogin(array $user): void
    {
        $attempts = ((int) ($user['wrong_attempts'] ?? 0)) + 1;
        $this->userModel->updateLoginAttempts((int) $user['id'], $attempts);
    }

    /**
     * Validate account state before login success.
     *
     * @param array<string, mixed> $user
     * @return array{
     *     success: bool,
     *     user: null,
     *     error: string,
     *     redirect: string|null
     * }|null
     */
    private function validateUserAccountState(array $user): ?array
    {
        if ((int) ($user['can_login'] ?? 1) !== 1) {
            return $this->failureResponse('Your account is blocked. Contact admin.');
        }

        if (empty($user['email_verified_at'])) {
            return $this->failureResponse(
                'Please verify your email before logging in.',
                '/verify-otp?email=' . urlencode((string) $user['email'])
            );
        }

        return null;
    }
}