<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use Throwable;

/**
 * Class AuthController
 * Handles user authentication including login, registration, and logout.
 */
class AuthController extends Controller
{
    /** @var User Instance of the User model */
    protected User $userModel;

    /**
     * Owner configurable settings
     */
    private int $maxAttempts = 3;
    private int $lockMinutes = 15;

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Handle user login requests.
     */
    public function login(): mixed
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->view('auth/login');
        }

        try {

            $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);

            $email = $data['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                return $this->view('auth/login', [
                    'error' => "Email not found.",
                    'data' => $data
                ]);
            }

            /**
             * Check account lock
             */
            if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {

                $diff = strtotime($user['lock_until']) - time();
                $minutes = ceil($diff / 60);

                return $this->view('auth/login', [
                    'error' => "Account locked. Try again in $minutes minutes.",
                    'data' => $data
                ]);
            }

            /**
             * Verify password
             */
            if (!password_verify($password, $user['password_hash'])) {
                return $this->handleFailedAttempt($user, $data);
            }

            /**
             * Check email verification
             */
            if ((int)$user['status'] === 0) {
                return $this->view('auth/login', [
                    'error' => "Please verify your email first!",
                    'data' => $data
                ]);
            }

            /**
             * Success
             */
            $this->userModel->resetAttempts($user['id']);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];

            header('Location: /dashboard');
            exit;

        } catch (Throwable $e) {

            error_log(
                date('Y-m-d H:i:s') .
                " > AuthController > login > " .
                $e->getMessage()
            );

            return $this->view('auth/login', [
                'error' => "Something went wrong. Please try again."
            ]);
        }
    }

    /**
     * Handle failed login attempts
     */
    private function handleFailedAttempt(array $user, array $data): mixed
    {
        $attempts = (int)$user['wrong_attempts'] + 1;
        $lockUntil = null;

        if ($attempts >= $this->maxAttempts) {

            $lockUntil = date(
                'Y-m-d H:i:s',
                strtotime("+{$this->lockMinutes} minutes")
            );

            /**
             * Send security alert to owner
             */
            $this->sendSecurityAlert($user['email']);
        }

        $this->userModel->updateLoginAttempts($user['id'], $attempts, $lockUntil);

        $remaining = $this->maxAttempts - $attempts;

        $msg = ($remaining > 0)
            ? "Invalid password. $remaining attempts left."
            : "Account locked for {$this->lockMinutes} minutes due to multiple failed attempts.";

        return $this->view('auth/login', [
            'error' => $msg,
            'data' => $data
        ]);
    }

    /**
     * Handle user registration
     */
    public function register(): mixed
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->view('auth/register');
        }

        try {

            $raw_data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);

            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            /**
             * Password validation
             */
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
                return $this->view('auth/register', [
                    'error' => 'Password must be 8+ chars with letters & numbers.'
                ]);
            }

            if ($password !== $confirmPassword) {
                return $this->view('auth/register', [
                    'error' => 'Passwords do not match!'
                ]);
            }

            if ($this->userModel->findByEmail($raw_data['email'])) {
                return $this->view('auth/register', [
                    'error' => 'Email already registered.'
                ]);
            }

            /**
             * Generate email verification token
             */
            $verificationToken = bin2hex(random_bytes(32));

            $raw_data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
            $raw_data['verification_token'] = $verificationToken;

            if ($this->userModel->create($raw_data)) {

                /**
                 * Send verification email
                 */
                $this->sendVerificationEmail($raw_data['email'], $verificationToken);

                header('Location: /login?registered=1');
                exit;
            }

            return $this->view('auth/register', [
                'error' => 'Registration failed. Please contact support.'
            ]);

        } catch (Throwable $e) {

            error_log(
                date('Y-m-d H:i:s') .
                " > AuthController > register > " .
                $e->getMessage()
            );

            return $this->view('auth/register', [
                'error' => "Internal server error."
            ]);
        }
    }

    /**
     * Email verification
     */
    public function verify(): void
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            echo "Invalid verification link.";
            exit;
        }

        if ($this->userModel->verifyEmail($token)) {
            echo "Email verified successfully. You can now login.";
        } else {
            echo "Verification failed.";
        }
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(string $email, string $token): void
    {
        $link = "http://localhost/verify?token=$token";

        $subject = "Verify Your Email";

        $message = "
        <h3>Email Verification</h3>
        <p>Please click the link below to verify your account:</p>
        <a href='$link'>$link</a>
        ";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: no-reply@inventorysystem.com";

        mail($email, $subject, $message, $headers);
    }

    /**
     * Send security alert to owner
     */
    private function sendSecurityAlert(string $userEmail): void
    {
        $ownerEmail = "owner@inventorysystem.com";

        $subject = "Security Alert: Multiple Failed Login Attempts";

        $message = "
        Warning!

        Multiple failed login attempts detected.

        User Email: $userEmail
        Time: " . date('Y-m-d H:i:s') . "
        IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "
        ";

        mail($ownerEmail, $subject, $message);
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();

        header('Location: /login');
        exit;
    }
}