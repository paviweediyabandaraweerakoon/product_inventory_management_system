<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email</title>
</head>
<body>
    <h2>Inventory Pro Email Verification</h2>

    <p>Hello <?= htmlspecialchars((string) $firstName, ENT_QUOTES, 'UTF-8'); ?>,</p>

    <p>Thank you for registering with Inventory Pro.</p>

    <p>Your verification code is:</p>
    <h3><?= htmlspecialchars((string) $otp, ENT_QUOTES, 'UTF-8'); ?></h3>

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