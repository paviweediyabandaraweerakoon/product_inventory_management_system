<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Helpers\SecurityHelper;
use App\Request\AuthRequest;
use Throwable;

/**
 * Class AuthController
 * * Handles user authentication processes including login, registration, and logout.
 * Acts as a mediator between the AuthRequest validation and AuthService logic.
 * * @package App\Controllers
 */
class AuthController extends Controller
{
    /**
     * @var AuthService The service handling core authentication logic.
     */
    private AuthService $authService;

    /**
     * AuthController constructor.
     * Initializes the controller and its dependencies.
     */
    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    /**
     * Handles the login request.
     * Displays the login view for GET requests and processes credentials for POST requests.
     * * @return void
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/login');
            return;
        }

        // Basic sanitization of the POST global
        $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);

        // Validate CSRF Integrity
        if (!SecurityHelper::validateCsrfToken($data['csrf_token'] ?? '')) {
            $this->view('auth/login', [
                'error' => 'Security token expired. Please refresh and try again.',
                'data'  => $data
            ]);
            return;
        }

        $request = new AuthRequest($data);
        $errors = $request->validateLogin();

        if (!empty($errors)) {
            $this->view('auth/login', [
                'error' => reset($errors),
                'data'  => $data
            ]);
            return;
        }

        try {
            $sanitized = $request->sanitized();
            $result = $this->authService->authenticate($sanitized['email'], $sanitized['password']);

            if (!$result['success']) {
                if (isset($result['redirect'])) {
                    $this->redirect($result['redirect']);
                    return;
                }
                $this->view('auth/login', [
                    'error' => $result['error'],
                    'data'  => $data
                ]);
                return;
            }

            $this->setSession($result['user']);
            $this->redirect('/dashboard');

        } catch (Throwable $e) {
            $this->handleError("Login Error", $e);
        }
    }

    /**
     * Handles the registration request.
     * Displays the registration view for GET requests and processes new user data for POST requests.
     * * @return void
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/register');
            return;
        }

        $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);

        if (!SecurityHelper::validateCsrfToken($data['csrf_token'] ?? '')) {
            $this->view('auth/register', [
                'error' => 'Security token invalid.',
                'data'  => $data
            ]);
            return;
        }

        $request = new AuthRequest($data);
        $errors = $request->validateRegister();

        if (!empty($errors)) {
            $this->view('auth/register', [
                'error' => reset($errors),
                'data'  => $data
            ]);
            return;
        }

        try {
            $sanitized = $request->sanitized();
            
            if ($this->authService->registerUser($sanitized)) {
                $this->redirect('/verify-otp?email=' . urlencode($sanitized['email']));
                return;
            }

            $this->view('auth/register', [
                'error' => 'An error occurred during registration. Please try again.',
                'data'  => $data
            ]);

        } catch (Throwable $e) {
            $this->handleError("Registration Error", $e);
        }
    }

    /**
     * Destroys the user session and logs the user out.
     * * @return void
     */
    public function destroy(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('/login');
    }

    /**
     * Sets the authenticated user's session data.
     * * @param array $user Associative array containing user details.
     * @return void
     */
    private function setSession(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role_id'] = (int)$user['role_id'];
    }

    /**
     * Logs errors and displays a generic server error page.
     * * @param string $context The context or location where the error occurred.
     * @param Throwable $e The caught exception or error.
     * @return void
     */
    private function handleError(string $context, Throwable $e): void
    {
        error_log("[$context] " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        http_response_code(500);
        require_once __DIR__ . '/../Views/errors/500.php';
        exit;
    }
}