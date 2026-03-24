<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333;">
    <h2>Inventory Pro Email Verification</h2>

    <p>Hello <?= htmlspecialchars((string) $firstName, ENT_QUOTES, 'UTF-8'); ?>,</p>

    <p>Thank you for registering with Inventory Pro.</p>

    <p>Your verification code is:</p>
    <p style="font-size: 24px; font-weight: bold; letter-spacing: 2px;">
        <?= htmlspecialchars((string) $otp, ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <p>This code will expire in <?= (int) $expiryMinutes; ?> minutes.</p>

    <p>You can continue verification here:</p>
    <p>
        <a href="<?= htmlspecialchars((string) $verifyPageUrl, ENT_QUOTES, 'UTF-8'); ?>">
            <?= htmlspecialchars((string) $verifyPageUrl, ENT_QUOTES, 'UTF-8'); ?>
        </a>
    </p>

    <p>If you did not create this account, please ignore this email.</p>
</body>
</html>