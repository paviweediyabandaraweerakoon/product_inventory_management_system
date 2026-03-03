<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="rounded-2xl bg-white p-8 shadow-xl w-full max-w-md">
        <div class="mb-8">
            <h2 class="mb-2 text-2xl font-bold text-gray-900">Create Account</h2>
            <p class="text-gray-600">Sign up to get started</p>
        </div>

        <form action="/register" method="POST" class="space-y-5">
            <div class="flex gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" class="w-full rounded-lg border border-gray-300 py-3 px-4 focus:ring-2 focus:ring-blue-500/20 outline-none" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" class="w-full rounded-lg border border-gray-300 py-3 px-4 focus:ring-2 focus:ring-blue-500/20 outline-none" required>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" class="w-full rounded-lg border border-gray-300 py-3 px-4 focus:ring-2 focus:ring-blue-500/20 outline-none" required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="w-full rounded-lg border border-gray-300 py-3 px-4 focus:ring-2 focus:ring-blue-500/20 outline-none" required>
            </div>

            <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 py-3 font-semibold text-white shadow-lg hover:shadow-xl transition-all">
                Create Account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Already have an account? <a href="/login" class="font-medium text-blue-600 hover:text-blue-700">Sign in</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../layouts/header.php'; ?>