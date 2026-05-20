<?php 
ob_start(); 
$avatarPath = !empty($user['avatar_path']) ? '/uploads/avatars/' . $user['avatar_path'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']);
?>

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/users" class="rounded-lg p-2 hover:bg-gray-100"><i data-lucide="arrow-left"></i></a>
            <h1 class="text-3xl font-bold text-gray-900">User Profile</h1>
        </div>
        <a href="/users/edit/<?= (int)$user['id'] ?>" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white shadow-md hover:bg-blue-700">
            <i data-lucide="user-cog" class="h-4 w-4"></i> Edit Profile
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="md:col-span-1 space-y-6">
            <div class="rounded-3xl border border-gray-100 bg-white p-6 text-center shadow-sm">
                <img src="<?= $avatarPath ?>" alt="Profile" class="mx-auto h-32 w-32 rounded-full border-4 border-blue-50 object-cover shadow-sm">
                <h2 class="mt-4 text-xl font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h2>
                <span class="mt-1 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase text-blue-700">
                    <?= htmlspecialchars($user['role']) ?>
                </span>
            </div>

            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Account Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-sm text-gray-500">Last Login</span>
                        <span class="text-sm font-medium text-gray-900"><?= $user['last_login'] ?? 'Never' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="rounded-3xl border border-gray-100 bg-white p-8 shadow-sm">
                <h3 class="mb-6 text-lg font-bold text-gray-900">Account Details</h3>
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400">Email Address</p>
                        <p class="mt-1 font-medium text-gray-900"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400">Account Status</p>
                        <p class="mt-1 flex items-center gap-2 font-medium text-emerald-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400">Member Since</p>
                        <p class="mt-1 font-medium text-gray-900"><?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>