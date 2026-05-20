<?php ob_start(); ?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/users" class="rounded-xl border border-gray-100 bg-white p-2 shadow-sm transition-colors hover:bg-gray-100">
            <i data-lucide="arrow-left" class="h-6 w-6 text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New User</h1>
            <p class="text-sm font-medium text-gray-500">Create a new system administrator or staff member</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
            <ul class="list-inside list-disc font-medium">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/users/store" method="POST" enctype="multipart/form-data" id="userForm" class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="grid grid-cols-1 gap-6 p-8 md:grid-cols-2">
            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="e.g. John Doe">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Email Address *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10" placeholder="name@company.com">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">System Role *</label>
                <select name="role" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    <option value="staff" <?= ($old['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Password *</label>
                <input type="password" name="password" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Confirm Password *</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
            </div>

            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Profile Picture</label>
                <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50/50 p-8">
            <a href="/users" class="rounded-xl px-6 py-3 text-sm font-bold text-gray-600 hover:bg-gray-200">Cancel</a>
            <button type="submit" class="rounded-xl bg-blue-600 px-10 py-3 font-bold text-white shadow-lg hover:bg-blue-700">Save User</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>