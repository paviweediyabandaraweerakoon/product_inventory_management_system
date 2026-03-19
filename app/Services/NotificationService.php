<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Env;
use Throwable;

/**
 * Handles system email notifications.
 */
class NotificationService
{
    /**
     * Send verification OTP email.
     *
     * @param string $email
     * @param string $firstName
     * @param string $otp
     * @param int $expiryMinutes
     * @return bool
     */
    public function sendVerificationOtpEmail(
        string $email,
        string $firstName,
        string $otp,
        int $expiryMinutes
    ): bool {
        $subject = 'Verify Your Email - Inventory Pro';
        $verifyPageUrl = rtrim(Env::get('APP_URL', 'http://localhost'), '/') .
            '/verify-otp?email=' . urlencode($email);

        $message = $this->renderTemplate('emails/verification-otp', [
            'firstName' => $firstName,
            'otp' => $otp,
            'expiryMinutes' => $expiryMinutes,
            'verifyPageUrl' => $verifyPageUrl,
        ]);

        if ($message === '') {
            error_log(date('Y-m-d H:i:s') . ' > NotificationService > Empty verification email body for ' . $email);

            return false;
        }

        return $this->sendHtmlEmail($email, $subject, $message);
    }

    /**
     * Render a PHP email template.
     *
     * @param string $template
     * @param array<string, mixed> $data
     * @return string
     */
    private function renderTemplate(string $template, array $data): string
    {
        $path = __DIR__ . '/../Views/' . $template . '.php';

        if (!file_exists($path)) {
            error_log(date('Y-m-d H:i:s') . ' > NotificationService > Email template not found: ' . $path);

            return '';
        }

        extract($data, EXTR_SKIP);

        ob_start();

        try {
            include $path;

            return (string) ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();

            error_log(
                date('Y-m-d H:i:s') .
                ' > NotificationService > Template render failed: ' .
                $e->getMessage()
            );

            return '';
        }
    }

    /**
     * Send an HTML email.
     *
     * @param string $to
     * @param string $subject
     * @param string $htmlMessage
     * @return bool
     */
    private function sendHtmlEmail(string $to, string $subject, string $htmlMessage): bool
    {
        $fromEmail = Env::get('MAIL_FROM_ADDRESS', 'no-reply@inventorypro.com');
        $fromName = Env::get('MAIL_FROM_NAME', 'Inventory Pro');

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
        ];

        $sent = mail($to, $subject, $htmlMessage, implode("\r\n", $headers));

        if (!$sent) {
            error_log(
                date('Y-m-d H:i:s') .
                ' > NotificationService > mail() failed for recipient: ' . $to
            );
        }

        return $sent;
    }
}