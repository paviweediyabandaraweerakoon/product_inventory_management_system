<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple environment variable loader.
 *
 * Loads key/value pairs from a .env file into:
 * - putenv()
 * - $_ENV
 * - $_SERVER
 */
class Env
{
    /**
     * Load environment variables from the given file path.
     *
     * @param string $path Absolute or relative path to the .env file.
     * @return void
     */
    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);

            if ($name === '') {
                continue;
            }

            $value = self::normalizeValue($value);

            if (!array_key_exists($name, $_ENV) && !array_key_exists($name, $_SERVER)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    /**
     * Get an environment variable value.
     *
     * @param string $key Environment variable name.
     * @param mixed $default Default value if key is not found.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Normalize .env values by trimming surrounding quotes.
     *
     * @param string $value Raw environment value.
     * @return string
     */
    private static function normalizeValue(string $value): string
    {
        $length = strlen($value);

        if ($length >= 2) {
            $first = $value[0];
            $last = $value[$length - 1];

            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                return substr($value, 1, -1);
            }
        }

        return $value;
    }
}