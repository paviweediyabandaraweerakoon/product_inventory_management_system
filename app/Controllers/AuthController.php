<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\SecurityHelper;
use App\Requests\AuthRequest;
use App\Services\AuthService;
use Throwable;

/**
 * Handles authentication HTTP flow.
 */
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Handle login request.
     *
     * @return void
     */
    public function login(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->view('auth/login');

                return;
            }

            $data = $this->getInputData();

            if (!$this->validateCsrfOrRender('auth/login', $data)) {
                return;
            }

            $request = new AuthRequest($data);
            $errors = $request->validateLogin();

            if (!empty($errors)) {
                $this->view('auth/login', [
                    'errors' => $errors,
                    'data' => $data,
                ]);

                return;
            }

            $result = $this->authService->authenticate(
                trim((string) ($data['email'] ?? '')),
                (string) ($data['password'] ?? '')
            );

            if ($result['success']) {
                $_SESSION['user'] = $result['user'];
                $this->redirect('/dashboard');

                return;
            }

            if (!empty($result['redirect'])) {
                $this->redirect($result['redirect']);

                return;
            }

            $this->view('auth/login', [
                'error' => $result['error'],
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            $this->handleServerError($e, 'AuthController@login');
        }
    }

    /**
     * Handle registration request.
     *
     * @return void
     */
    public function register(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->view('auth/register');

                return;
            }

            $data = $this->getInputData();

            if (!$this->validateCsrfOrRender('auth/register', $data)) {
                return;
            }

            $request = new AuthRequest($data);
            $errors = $request->validateRegister();

            if (!empty($errors)) {
                $this->view('auth/register', [
                    'errors' => $errors,
                    'data' => $data,
                ]);

                return;
            }

            $sanitized = $request->sanitized();
            $registerResult = $this->authService->registerUser($sanitized);

            if ($registerResult['success']) {
                $this->redirect((string) $registerResult['redirect']);

                return;
            }

            $this->view('auth/register', [
                'error' => $registerResult['error'],
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            $this->handleServerError($e, 'AuthController@register');
        }
    }

    /**
     * Get normalized POST input.
     *
     * Important:
     * - Trims string inputs
     * - Output escaping must happen in the view layer
     *
     * @return array<string, mixed>
     */
    private function getInputData(): array
    {
        $data = [];

        foreach ($_POST as $key => $value) {
            $data[$key] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    /**
     * Validate CSRF token and render error view if invalid.
     *
     * @param string $view
     * @param array<string, mixed> $data
     * @return bool
     */
    private function validateCsrfOrRender(string $view, array $data): bool
    {
        if (!SecurityHelper::validateCsrfToken($data['csrf_token'] ?? null)) {
            $this->view($view, [
                'error' => 'Security token expired. Please refresh and try again.',
                'data' => $data,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Handle unexpected server errors.
     *
     * @param Throwable $e
     * @param string $context
     * @return void
     */
    private function handleServerError(Throwable $e, string $context): void
    {
        error_log(sprintf(
            '[%s] %s in %s on line %d',
            $context,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));

        http_response_code(500);

        $this->view('errors/500');
    }
}