<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Env;
use Throwable;

/**
 * Class AuthController
 * Manages the user authentication lifecycle including secure login, 
 * account registration, email verification, and session management.
 */
class AuthController extends Controller
{
    /** @var User Instance of the User model */
    private User $userModel;

    /** @var int Maximum failed login attempts allowed before lockout */
    private int $maxAttempts;

    /** @var int Duration of account lockout in minutes */
    private int $lockMinutes;

    /**
     * AuthController constructor.
     * Initializes models and configuration from environment variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        
        // Configuration: Using Env class for professional standard
        $this->maxAttempts = (int) Env::get('AUTH_MAX_ATTEMPTS', 3);
        $this->lockMinutes = (int) Env::get('AUTH_LOCK_MINUTES', 15);
    }

    /**
     * Handle user login requests with account lockout protection.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/login');
            return;
        }

        try {
            // Data Sanitization
            $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);
            $email = $data['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                $this->view('auth/login', [
                    'error' => "Email not found.",
                    'data' => $data
                ]);
                return;
            }

            // Check Account Lock Status
            if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {
                $diff = strtotime($user['lock_until']) - time();
                $minutes = ceil($diff / 60);

                $this->view('auth/login', [
                    'error' => "Account locked. Try again in $minutes minutes.",
                    'data' => $data
                ]);
                return;
            }

            // Verify Password
            if (!password_verify($password, $user['password_hash'])) {
                $this->handleFailedAttempt($user, $data);
                return;
            }

            // Check Email Verification Status
            if (isset($user['status']) && (int)$user['status'] === 0) {
                $this->view('auth/login', [
                    'error' => "Please verify your email first!",
                    'data' => $data
                ]);
                return;
            }

            // Authentication Success
            $this->userModel->resetAttempts($user['id']);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];

            header('Location: /dashboard');
            exit;

        } catch (Throwable $e) {
            $this->logError('Login', $e);
            $this->view('auth/login', ['error' => "Something went wrong. Please try again."]);
            exit;
        }
    }

    /**
     * Handle failed login attempts and trigger lockout if necessary.
     */
    private function handleFailedAttempt(array $user, array $data): void
    {
        $attempts = (int)$user['wrong_attempts'] + 1;
        $lockUntil = null;

        if ($attempts >= $this->maxAttempts) {
            $lockUntil = date('Y-m-d H:i:s', strtotime("+{$this->lockMinutes} minutes"));
            $this->sendSecurityAlert($user['email']);
        }

        $this->userModel->updateLoginAttempts($user['id'], $attempts, $lockUntil);

        $remaining = $this->maxAttempts - $attempts;
        $msg = ($remaining > 0)
            ? "Invalid password. $remaining attempts left."
            : "Account locked for {$this->lockMinutes} minutes due to multiple failed attempts.";

        $this->view('auth/login', [
            'error' => $msg,
            'data' => $data
        ]);
    }

    /**
     * Handle user registration with password complexity validation.
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/register');
            return;
        }

        try {
            $raw_data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Password Complexity Validation
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
                $this->view('auth/register', ['error' => 'Password must be 8+ chars with letters & numbers.', 'data' => $raw_data]);
                return;
            }

            if ($password !== $confirmPassword) {
                $this->view('auth/register', ['error' => 'Passwords do not match!', 'data' => $raw_data]);
                return;
            }

            if ($this->userModel->findByEmail($raw_data['email'])) {
                $this->view('auth/register', ['error' => 'Email already registered.', 'data' => $raw_data]);
                return;
            }

            // Security Token Generation
            $verificationToken = bin2hex(random_bytes(32));
            $raw_data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
            $raw_data['verification_token'] = $verificationToken;

            if ($this->userModel->create($raw_data)) {
                $this->sendVerificationEmail($raw_data['email'], $verificationToken);
                header('Location: /login?registered=1');
                exit;
            }

            $this->view('auth/register', ['error' => 'Registration failed. Please contact support.', 'data' => $raw_data]);

        } catch (Throwable $e) {
            $this->logError('Register', $e);
            $this->view('auth/register', ['error' => "Internal server error."]);
            exit;
        }
    }

    /**
     * Handle email verification from token link.
     */
    public function verify(): void
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            echo "Invalid verification link.";
            exit;
        }

        if ($this->userModel->verifyEmail($token)) {
            header('Location: /login?verified=1');
        } else {
            echo "Verification failed or token expired.";
        }
        exit;
    }

    /**
     * Send verification email to the user.
     */
    private function sendVerificationEmail(string $email, string $token): void
    {
        $baseUrl = rtrim(Env::get('APP_URL', 'http://localhost'), '/');
        $link = "{$baseUrl}/verify?token={$token}";

        $subject = "Verify Your Email";
        $message = "<h3>Email Verification</h3><p>Please click the link below to verify your account:</p><a href='$link'>$link</a>";

        $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: no-reply@inventorysystem.com";
        mail($email, $subject, $message, $headers);
    }

    /**
     * Logout user and clear session.
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * Standardized error logging logic.
     */
    private function logError(string $action, Throwable $e): void
    {
        error_log(sprintf(
            "[%s] AuthController %s Error: %s in %s on line %d",
            date('Y-m-d H:i:s'), $action, $e->getMessage(), $e->getFile(), $e->getLine()
        ));
    }
}