<?php
ob_start();
?>
<form action="/login" method="POST" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-700">Email Address</label>
        <input type="email" name="email" required
               value="<?= htmlspecialchars((string)($formData['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
               placeholder="admin@inventory.com"
               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
    </div>
    <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-700">Password</label>
        <input type="password" name="password" required placeholder="Enter your password"
               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
    </div>
    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center space-x-2">
        <span>Sign In</span><i data-lucide="arrow-right" class="w-4 h-4"></i>
    </button>
</form>
<div class="mt-8 text-center">
    <p class="text-slate-500 text-sm">Don't have an account? <a href="/register" class="text-indigo-600 font-bold hover:underline">Create account</a></p>
</div>
<?php
$content = ob_get_clean();
$isRegisterPage = false;
$title = "Login - Inventory Pro";
require_once __DIR__ . '/../layouts/auth_layout.php';