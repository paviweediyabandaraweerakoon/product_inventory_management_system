<?php ob_start(); ?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/users" class="rounded-xl border border-gray-100 bg-white p-2 shadow-sm hover:bg-gray-100">
            <i data-lucide="arrow-left" class="h-6 w-6 text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="text-sm font-medium text-gray-500">Updating profile for: <span class="text-blue-600"><?= htmlspecialchars($user['email']) ?></span></p>
        </div>
    </div>

    <form action="/users/update/<?= (int)$user['id'] ?>" method="POST" enctype="multipart/form-data" class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="grid grid-cols-1 gap-6 p-8 md:grid-cols-2">
            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Email Address *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Role *</label>
                <select name="role" required class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                </select>
            </div>

            <div class="col-span-2 mt-4 rounded-2xl bg-gray-50 p-6">
                <h3 class="mb-4 text-sm font-bold text-gray-900 uppercase tracking-wider">Change Password (Leave blank to keep current)</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500">New Password</label>
                        <input type="password" name="password" class="w-full rounded-lg border border-gray-200 px-4 py-2 outline-none focus:border-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border border-gray-200 px-4 py-2 outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50/50 p-8">
            <button type="submit" class="rounded-xl bg-blue-600 px-10 py-3 font-bold text-white shadow-lg hover:bg-blue-700">Update Profile</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>