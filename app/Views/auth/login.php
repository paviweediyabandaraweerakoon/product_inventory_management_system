<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="rounded-2xl bg-white p-8 shadow-xl w-full max-w-md">
        <div class="mb-8">
            <h2 class="mb-2 text-2xl font-bold text-gray-900">Welcome Back</h2>
            <p class="text-gray-600">Sign in to your account to continue</p>
        </div>

        <?php session_start(); if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-6">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" class="w-full rounded-lg border border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all" placeholder="admin@example.com" required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="w-full rounded-lg border border-gray-300 py-3 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 py-3 font-semibold text-white shadow-lg hover:shadow-xl transition-all">
                Sign In
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Don't have an account? <a href="/register" class="font-medium text-blue-600 hover:text-blue-700">Create account</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../layouts/header.php'; ?>