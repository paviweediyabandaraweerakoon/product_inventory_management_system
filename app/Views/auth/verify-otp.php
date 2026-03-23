<?php
session_start();

// Flash messages (success/error) from session
$success = $_SESSION['flash_success'] ?? null;
$error = $_SESSION['flash_error'] ?? ($error ?? null);
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Pre-fill email safely
$emailValue = htmlspecialchars($data['email'] ?? ($_GET['email'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Inventory Pro</title>
    <link rel="stylesheet" href="/assets/css/tailwind-output.css">
    <script src="/assets/js/lucide.min.js"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0f172a] p-4 font-sans text-white">
    <div class="bg-white w-full max-w-md p-10 rounded-[2rem] shadow-2xl text-slate-800">
        <h2 class="text-3xl font-bold mb-2">Verify Your Email</h2>
        <p class="text-slate-500 mb-6">Enter the OTP sent to your email.</p>

        <?php if ($success): ?>
            <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/verify-otp" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div>
                <label class="text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" required
                       value="<?= $emailValue ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200">
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">OTP Code</label>
                <input type="text" name="otp" required maxlength="6"
                       placeholder="Enter 6-digit OTP"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl">
                Verify OTP
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/resend-otp?email=<?= urlencode($emailValue) ?>"
               class="text-indigo-600 font-bold hover:underline">
                Resend OTP
            </a>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>