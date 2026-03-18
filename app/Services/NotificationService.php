<?php

namespace App\Services;

use App\Core\Env;

/**
 * Class NotificationService
 *
 * Handles notification dispatching for:
 * - Email verification OTP
 * - Security alerts (owner/admin)
 * - SMS placeholder integration
 */
class NotificationService
{
    /**
     * Send email verification OTP.
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

        $message = "
            <h3>Hello {$firstName},</h3>
            <p>Your email verification OTP is:</p>
            <h2>{$otp}</h2>
            <p>This OTP will expire in {$expiryMinutes} minute(s).</p>
        ";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: " . Env::get('MAIL_FROM_ADDRESS', 'no-reply@inventorypro.com') . "\r\n";

        return mail($email, $subject, $message, $headers);
    }

    /**
     * Send security alert to system owner/admin when user gets locked.
     *
     * @param string $userEmail
     * @param string|null $phoneNumber
     * @param int $lockMinutes
     * @return void
     */
    public function sendOwnerSecurityAlert(string $userEmail, ?string $phoneNumber, int $lockMinutes): void
    {
        $ownerEmail = Env::get('SECURITY_ALERT_EMAIL', 'owner@example.com');
        $ownerPhone = Env::get('SECURITY_ALERT_PHONE', '');

        $subject = 'Security Alert - Account Locked';
        $message = "
            <h3>Security Alert</h3>
            <p>User account with email <strong>{$userEmail}</strong> has been temporarily locked.</p>
            <p>Lock duration: <strong>{$lockMinutes} minute(s)</strong></p>
            <p>User phone (if available): <strong>" . ($phoneNumber ?: 'N/A') . "</strong></p>
        ";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: " . Env::get('MAIL_FROM_ADDRESS', 'no-reply@inventorypro.com') . "\r\n";

        mail($ownerEmail, $subject, $message, $headers);

        $this->sendSmsAlert($ownerPhone, "Security Alert: User {$userEmail} locked for {$lockMinutes} min.");
    }

    /**
     * Placeholder SMS integration.
     * Replace with real provider later (Twilio / Notify.lk / etc.).
     *
     * @param string $phone
     * @param string $message
     * @return void
     */
    private function sendSmsAlert(string $phone, string $message): void
    {
        if (empty($phone)) {
            return;
        }

        error_log(sprintf(
            '%s > NotificationService > sendSmsAlert > SMS placeholder to %s > %s',
            date('Y-m-d H:i:s'),
            $phone,
            $message
        ));
    }
}