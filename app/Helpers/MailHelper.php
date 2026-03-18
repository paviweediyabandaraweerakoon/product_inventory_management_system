<?php

namespace App\Helpers;

/**
 * Class MailHelper
 * Handles email notifications for account verification and security alerts.
 */
class MailHelper
{
    /**
     * Sends a verification email to the user.
     *
     * @param string $email
     * @param string $token
     * @return bool
     */
    public static function sendVerification(string $email, string $token): bool
    {
        $link = "http://localhost/verify?token=" . urlencode($token);
        $subject = "Verify Your Account - Inventory Pro";

        $message = "
        <html>
        <head><title>Email Verification</title></head>
        <body>
            <h2>Inventory Pro Email Verification</h2>
            <p>Thank you for signing up. Click below to verify your account:</p>
            <a href='" . htmlspecialchars($link) . "'>" . htmlspecialchars($link) . "</a>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: no-reply@inventorypro.com\r\n";

        return mail($email, $subject, $message, $headers);
    }

    /**
     * Sends a security alert to admin on failed login attempts.
     *
     * @param string $email
     * @return bool
     */
    public static function securityAlert(string $email): bool
    {
        $subject = "Security Alert - Multiple Failed Logins";
        $message = "Multiple failed login attempts detected for: " . htmlspecialchars($email) .
                   "\nTime: " . date('Y-m-d H:i:s');

        $headers = "From: security-alerts@inventorypro.com\r\n";

        return mail("owner@inventory.com", $subject, $message, $headers);
    }
}