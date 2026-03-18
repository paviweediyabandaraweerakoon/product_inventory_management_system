<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Env;
use App\Models\User;
use App\Services\NotificationService;
use App\Helpers\SecurityHelper;
use App\Helpers\MailHelper;
use App\Request\AuthRequest;
use Throwable;

/**
 * Class AuthController
 * Handles authentication: login, register, logout
 */
class AuthController extends Controller
{
    private User $userModel;
    private NotificationService $notificationService;
    private int $maxAttempts;
    private int $lockMinutes;
    private int $otpExpiryMinutes;
    private int $defaultRoleId;

    /**
     * Initialize controller dependencies and settings.
     */
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->notificationService = new NotificationService();

        $this->maxAttempts = (int)Env::get('AUTH_MAX_ATTEMPTS', 3);
        $this->lockMinutes = (int)Env::get('AUTH_LOCK_MINUTES', 15);
        $this->otpExpiryMinutes = (int)Env::get('AUTH_OTP_EXPIRY_MINUTES', 10);
        $this->defaultRoleId = (int)Env::get('AUTH_DEFAULT_STAFF_ROLE_ID', 3);
    }

    /**
     * Handle login requests.
     *
     * @return void
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/login');
            return;
        }

        $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);

        // CSRF Validation
        $csrfToken = $data['csrf_token'] ?? '';
        if (!SecurityHelper::validateCsrfToken($csrfToken)) {
            $this->view('auth/login', ['error' => 'Invalid CSRF token.', 'data' => $data]);
            return;
        }

        $request = new AuthRequest($data);
        $errors = $request->validateLogin();
        if ($errors) {
            $this->view('auth/login', ['error' => reset($errors), 'data' => $data]);
            return;
        }

        $sanitized = $request->sanitized();

        try {
            $user = $this->userModel->findByEmail($sanitized['email']);

            if (!$user || !password_verify($sanitized['password'], $user['password_hash'])) {
                $this->handleFailedLogin($user);
                return;
            }

            if (!$this->userModel->isEmailVerified($user)) {
                $this->redirect('/verify-otp?email=' . urlencode($sanitized['email']));
                return;
            }

            $this->userModel->resetLoginAttempts((int)$user['id']);
            $this->setSession($user);
            $this->redirect('/dashboard');

        } catch (Throwable $e) {
            error_log("Login Error: " . $e->getMessage());
            http_response_code(500);
            require_once __DIR__ . '/../Views/errors/500.php';
            exit;
        }
    }

    /**
     * Handle registration requests.
     *
     * @return void
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/register');
            return;
        }

        $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);

        // CSRF Validation
        $csrfToken = $data['csrf_token'] ?? '';
        if (!SecurityHelper::validateCsrfToken($csrfToken)) {
            $this->view('auth/register', ['error' => 'Invalid CSRF token.', 'data' => $data]);
            return;
        }

        $request = new AuthRequest($data);
        $errors = $request->validateRegister();
        if ($errors) {
            $this->view('auth/register', ['error' => reset($errors), 'data' => $data]);
            return;
        }

        $sanitized = $request->sanitized();

        if ($this->userModel->emailExists($sanitized['email'])) {
            $this->view('auth/register', ['error' => 'Email already exists.', 'data' => $sanitized]);
            return;
        }

        [$firstName, $lastName] = SecurityHelper::splitName($sanitized['full_name']);
        $otp = SecurityHelper::generateOtp();
        $otpExpiresAt = date('Y-m-d H:i:s', strtotime("+{$this->otpExpiryMinutes} minutes"));

        try {
            $userId = $this->userModel->createUser([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $sanitized['email'],
                'password_hash' => password_hash($sanitized['password'], PASSWORD_BCRYPT),
                'role_id' => $this->defaultRoleId,
                'status' => 1,
                'can_login' => 1,
                'wrong_attempts' => 0,
                'lockout_duration' => $this->lockMinutes,
                'email_verification_otp' => $otp,
                'email_verification_otp_expires_at' => $otpExpiresAt,
            ]);

            $this->notificationService->sendVerificationOtpEmail($sanitized['email'], $firstName, $otp, $this->otpExpiryMinutes);
            $this->redirect('/verify-otp?email=' . urlencode($sanitized['email']));

        } catch (Throwable $e) {
            error_log("Registration Error: " . $e->getMessage());
            http_response_code(500);
            require_once __DIR__ . '/../Views/errors/500.php';
            exit;
        }
    }

    /**
     * Logout the user and destroy session.
     *
     * @return void
     */
    public function destroy(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('/login');
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    /**
     * Set session data for logged-in user.
     *
     * @param array $user
     * @return void
     */
    private function setSession(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role_id'] = (int)$user['role_id'];
    }

    /**
     * Handle failed login attempts with lockout mechanism.
     *
     * @param array|null $user
     * @return void
     */
    private function handleFailedLogin(?array $user): void
    {
        $attempts = ($user['wrong_attempts'] ?? 0) + 1;
        $lockUntil = $attempts >= $this->maxAttempts
            ? date('Y-m-d H:i:s', strtotime("+{$this->lockMinutes} minutes"))
            : null;

        if ($user) {
            $this->userModel->updateLoginAttempts((int)$user['id'], $attempts, $lockUntil);
            if ($lockUntil) MailHelper::securityAlert($user['email']);
        }

        $message = $lockUntil
            ? "Account locked for {$this->lockMinutes} mins."
            : "Invalid credentials. " . max(0, $this->maxAttempts - $attempts) . " attempts left.";

        $this->view('auth/login', ['error' => $message, 'data' => $user ?? []]);
    }
}