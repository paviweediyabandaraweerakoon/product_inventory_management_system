<?php
session_start();

/** @var string $message */
// Flash message from session (if any)

$message = $_SESSION['flash_message'] ?? ($message ?? 'You do not have permission to log in.');
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - Inventory Pro</title>
    <link rel="stylesheet" href="/assets/css/tailwind-output.css">
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0f172a] p-4 font-sans text-white">
    <div class="bg-white w-full max-w-md p-10 rounded-[2rem] shadow-2xl text-slate-800 text-center">
        <h2 class="text-3xl font-bold mb-4 text-amber-600">Access Denied</h2>
        <p class="text-slate-600 mb-6"><?= htmlspecialchars($message) ?></p>
        <a href="/login" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl">
            Back to Login
        </a>
    </div>
</body>
</html>