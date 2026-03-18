<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** @var string $title */
/** @var string $heading */
/** @var string $message */
/** @var string $icon */
/** @var string $iconWrapperClass */
/** @var string $headingClass */
/** @var string $buttonText */
/** @var string $buttonHref */

$title = $title ?? 'Status - Inventory Pro';
$heading = $heading ?? 'Notice';
$message = $message ?? 'Something happened.';
$icon = $icon ?? 'shield-alert';
$iconWrapperClass = $iconWrapperClass ?? 'bg-amber-500/20 text-amber-300';
$headingClass = $headingClass ?? 'text-amber-300';
$buttonText = $buttonText ?? 'Back to Login';
$buttonHref = $buttonHref ?? '/login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/tailwind-output.css">
    <script src="/assets/js/lucide.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-purple-950 text-white font-sans">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center">
            
            <!-- Left Branding Panel -->
            <div class="hidden lg:block">
                <div class="mb-10 flex items-center gap-4">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-3xl shadow-2xl">
                        <i data-lucide="box" class="w-10 h-10 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-5xl font-extrabold tracking-tight">Inventory Pro</h1>
                        <p class="text-slate-300 text-xl mt-1">Professional Management System</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="rounded-3xl border border-white/10 bg-white/10 backdrop-blur-md p-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-emerald-500/20 text-emerald-300">
                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Secure Access Control</h3>
                                <p class="text-slate-300 mt-2">
                                    User authentication and role-based restrictions keep your inventory system protected.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/10 backdrop-blur-md p-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-violet-500/20 text-violet-300">
                                <i data-lucide="lock" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Reliable Account Security</h3>
                                <p class="text-slate-300 mt-2">
                                    Security workflows ensure only authorized users can access critical inventory data.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/10 backdrop-blur-md p-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-blue-500/20 text-blue-300">
                                <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Unified System Experience</h3>
                                <p class="text-slate-300 mt-2">
                                    Consistent interfaces across login, registration, and status pages improve usability.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Status Card -->
            <div class="flex justify-center lg:justify-end">
                <div class="w-full max-w-xl rounded-[2rem] bg-white/95 text-slate-900 shadow-2xl p-8 sm:p-10">
                    <div class="mb-8 text-center">
                        <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl <?= htmlspecialchars($iconWrapperClass, ENT_QUOTES, 'UTF-8') ?>">
                            <i data-lucide="<?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>" class="w-10 h-10"></i>
                        </div>

                        <h2 class="text-4xl font-extrabold <?= htmlspecialchars($headingClass, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed">
                            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 text-center mb-6">
                        Please contact the system administrator if you believe this message is incorrect.
                    </div>

                    <a href="<?= htmlspecialchars($buttonHref, ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white font-bold shadow-lg transition-all hover:from-indigo-700 hover:to-purple-700">
                        <span><?= htmlspecialchars($buttonText, ENT_QUOTES, 'UTF-8') ?></span>
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>