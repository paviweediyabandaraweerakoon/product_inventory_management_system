<?php
namespace App\Core;

use App\Core\Env;
use Throwable;

/**
 * Class Controller
 * Base controller that provides common functionality such as:
 * - session initialization
 * - view rendering
 * - URL redirection
 * - standardized error logging
 */
class Controller
{
    /**
     * Initialize common controller dependencies.
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Render a view file and extract data for use in the template.
     *
     * @param string $view Path to the view file (e.g., 'categories/index')
     * @param array  $data Data to be passed to the view
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $file = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }

        $this->logError(static::class . ' View', new \RuntimeException(
            "View file [{$view}] not found at: {$file}"
        ));

        http_response_code(500);
        die('Error: View file not found.');
    }

    /**
     * Reusable helper for sanitizing POST data
     */
    protected function getPostData(): array
    {
        return array_map(function($value) {
            return is_string($value) ? htmlspecialchars(trim($value)) : $value;
        }, $_POST);
    }

    /**
     * Redirect to a path using APP_URL from .env.
     *
     * @param string $path Relative application path
     */
    protected function redirect(string $path): void
    {
        $baseUrl = rtrim((string) Env::get('APP_URL', ''), '/');
        header('Location: ' . $baseUrl . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Standardized application error logging for controllers.
     *
     * @param string    $action Action or context name
     * @param Throwable $e      Exception or throwable instance
     */
    protected function logError(string $action, Throwable $e): void
    {
        error_log(sprintf(
            '[%s] %s %s Error: %s in %s on line %d',
            date('Y-m-d H:i:s'),
            static::class,
            $action,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));
    }
}