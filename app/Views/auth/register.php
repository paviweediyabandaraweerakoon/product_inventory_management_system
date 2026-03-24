<?php
$formData = $data ?? [];

ob_start();
?>
<form action="/register" method="POST" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>">

    <div class="space-y-1">
        <label for="full_name" class="text-sm font-semibold text-slate-700">Full Name</label>
        <input
            id="full_name"
            type="text"
            name="full_name"
            required
            value="<?= htmlspecialchars((string) ($formData['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            placeholder="John Doe"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
        >
    </div>

    <div class="space-y-1">
        <label for="email" class="text-sm font-semibold text-slate-700">Email Address</label>
        <input
            id="email"
            type="email"
            name="email"
            required
            value="<?= htmlspecialchars((string) ($formData['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            placeholder="your@email.com"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
        >
    </div>

    <div class="space-y-1">
        <label for="password" class="text-sm font-semibold text-slate-700">Password</label>
        <input
            id="password"
            type="password"
            name="password"
            required
            placeholder="Min 8 characters"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
        >
    </div>

    <div class="space-y-1">
        <label for="confirm_password" class="text-sm font-semibold text-slate-700">Confirm Password</label>
        <input
            id="confirm_password"
            type="password"
            name="confirm_password"
            required
            placeholder="Re-enter password"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
        >
    </div>

    <button
        type="submit"
        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-2 mt-4"
    >
        <span>Create Account</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
    </button>
</form>

<div class="mt-8 text-center">
    <p class="text-slate-500 text-sm">
        Already have an account?
        <a href="/login" class="text-indigo-600 font-bold hover:underline">
            Sign in here
        </a>
    </p>
</div>
<?php
$content = ob_get_clean();
$isRegisterPage = true;
$title = 'Register - Inventory Pro';
require_once __DIR__ . '/../layouts/auth_layout.php';