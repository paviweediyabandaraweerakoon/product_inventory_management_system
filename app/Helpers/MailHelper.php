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
     * * @param string $email The recipient's email address.
     * @param string $token The unique verification token.
     * @return bool Returns true if mail was sent, false otherwise.
     */
    public static function sendVerification(string $email, string $token): bool
    {
        // Logic remains the same as requested
        $link = "http://localhost/verify?token=" . $token;

        $subject = "Verify Your Account - Inventory Pro";

        $message = "
        <html>
        <head>
            <title>Email Verification</title>
        </head>
        <body>
            <h2>Inventory Pro Email Verification</h2>
            <p>Thank you for signing up. Please click the link below to verify your account:</p>
            <a href='" . htmlspecialchars($link) . "'>" . htmlspecialchars($link) . "</a>
            <br><br>
            <p>If you did not request this, please ignore this email.</p>
        </body>
        </html>
        ";

        // Headers formatted according to standards
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: no-reply@inventorypro.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Using mail() function as in original logic
        return mail($email, $subject, $message, $headers);
    }

    /**
     * Sends a security alert to the administrator for multiple failed login attempts.
     * * @param string $email The email address associated with the failed attempts.
     * @return bool
     */
    public static function securityAlert(string $email): bool
    {
        $subject = "Security Alert - Multiple Failed Logins";

        $message = "
        Warning!

        Multiple failed login attempts detected
        for account: " . htmlspecialchars($email) . "
        Timestamp: " . date('Y-m-d H:i:s') . "
        ";

        $headers = "From: security-alerts@inventorypro.com\r\n";

        // Logic remains same: Notify owner
        return mail("owner@inventory.com", $subject, $message, $headers);
    }
}