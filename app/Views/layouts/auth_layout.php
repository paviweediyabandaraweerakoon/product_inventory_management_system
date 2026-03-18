<?php
if (session_status() === PHP_SESSION_NONE) session_start();
use App\Helpers\SecurityHelper;

/** @var string $title */
/** @var string $error */
/** @var bool $isRegisterPage */
/** @var array $formData */

$formData = $formData ?? [];
$csrfToken = SecurityHelper::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'Inventory Pro', ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="/assets/css/tailwind-output.css">
<script src="/assets/js/lucide.min.js"></script>
<style>
    .glass-card {
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.1);
    }
</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 font-sans text-white p-4">

<div class="w-full max-w-6xl grid lg:grid-cols-2 gap-12 items-center">
    <!-- Left Panel (Same for Login/Register) -->
    <div class="hidden lg:block space-y-8">
        <div class="flex items-center space-x-4 mb-10">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-2xl shadow-xl">
                <i data-lucide="box" class="w-10 h-10 text-white"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold tracking-tight"><?= $isRegisterPage ? 'Join Inventory Pro' : 'Inventory Pro' ?></h1>
                <p class="text-slate-400"><?= $isRegisterPage ? 'Start managing smarter today' : 'Professional Management System' ?></p>
            </div>
        </div>
        <div class="space-y-6">
            <div class="glass-card p-4 rounded-2xl flex items-start space-x-4">
                <div class="bg-emerald-500/20 p-3 rounded-xl text-emerald-400">
                    <i data-lucide="shield-check"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Secure & Reliable</h3>
                    <p class="text-slate-400 text-sm">Enterprise-grade security with encrypted data protection</p>
                </div>
            </div>
            <div class="glass-card p-4 rounded-2xl flex items-start space-x-4">
                <div class="bg-purple-500/20 p-3 rounded-xl text-purple-400">
                    <i data-lucide="zap"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Fast & Efficient</h3>
                    <p class="text-slate-400 text-sm">Real-time inventory tracking and analytics dashboard</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel (Form injected via $content) -->
    <div class="w-full flex justify-center lg:justify-end">
        <div class="bg-white w-full max-w-md p-10 rounded-[2rem] shadow-2xl text-slate-800">
            <div class="mb-8">
                <h2 class="text-3xl font-bold mb-2 text-slate-900"><?= $isRegisterPage ? 'Create Account' : 'Welcome Back' ?></h2>
                <?php if (!empty($error)): ?>
                <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg flex items-center">
                    <i data-lucide="alert-triangle" class="mr-3 w-5 h-5 text-red-500"></i>
                    <span><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Inject page-specific form -->
            <?= $content ?>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
</body>
</html>