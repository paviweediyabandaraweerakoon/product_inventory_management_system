<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Helpers\SecurityHelper;

/** @var string $title */
/** @var string $error */
/** @var bool $isRegisterPage */
/** @var array $formData */
/** @var string $content */

$formData = $formData ?? [];
$csrfToken = SecurityHelper::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Inventory Pro', ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Local assets -->
    <link href="/public/assets/css/output.css" rel="stylesheet">
    <script src="/public/assets/js/lucide.js"></script>

    <style>
        .glass-card {
            background: rgba(99, 102, 241, 0.18);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-indigo-900 to-purple-900 font-sans text-white p-4 overflow-x-hidden">

    <div class="w-full max-w-7xl grid lg:grid-cols-2 gap-12 items-center">
        <!-- Left Panel -->
        <div class="hidden lg:block space-y-8">
            <div class="flex items-center gap-4 mb-10">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-2xl shadow-xl">
                    <i data-lucide="box" class="w-10 h-10 text-white"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-bold tracking-tight">
                        <?= $isRegisterPage ? 'Join Inventory Pro' : 'Inventory Pro' ?>
                    </h1>
                    <p class="text-slate-300">
                        <?= $isRegisterPage ? 'Start managing smarter today' : 'Professional Management System' ?>
                    </p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card p-6 rounded-3xl flex items-start gap-5">
                    <div class="bg-emerald-500/20 p-4 rounded-2xl text-emerald-400">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-2xl text-white">Secure & Reliable</h3>
                        <p class="text-slate-300 text-lg">
                            Enterprise-grade security with encrypted data protection
                        </p>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-3xl flex items-start gap-5">
                    <div class="bg-purple-500/20 p-4 rounded-2xl text-purple-300">
                        <i data-lucide="zap" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-2xl text-white">Fast & Efficient</h3>
                        <p class="text-slate-300 text-lg">
                            Real-time inventory tracking and analytics dashboard
                        </p>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-3xl flex items-start gap-5">
                    <div class="bg-indigo-500/20 p-4 rounded-2xl text-indigo-300">
                        <i data-lucide="package" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-2xl text-white">Complete Management</h3>
                        <p class="text-slate-300 text-lg">
                            Full product lifecycle and category management
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="w-full flex justify-center lg:justify-end">
            <div class="bg-white w-full max-w-2xl p-10 md:p-12 rounded-[2rem] shadow-2xl text-slate-800">
                <div class="mb-8">
                    <h2 class="text-4xl font-bold mb-3 text-slate-900">
                        <?= $isRegisterPage ? 'Create Account' : 'Welcome Back' ?>
                    </h2>
                    <p class="text-slate-500 text-xl">
                        <?= $isRegisterPage ? 'Create your account to get started' : 'Sign in to access your inventory dashboard' ?>
                    </p>

                    <?php if (!empty($error)): ?>
                        <div class="mt-5 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl flex items-center gap-3">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                            <span><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?= $content ?>

                <?php if (!$isRegisterPage): ?>
                    <div class="mt-8 rounded-2xl border border-indigo-100 bg-indigo-50/70 px-6 py-5 text-center text-slate-600">
                        <span class="mr-2">💡</span>
                        <span class="font-medium">Demo: Use any email and password to login</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>