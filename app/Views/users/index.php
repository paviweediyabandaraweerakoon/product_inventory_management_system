<?php
ob_start();
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">User Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your team members and their access permissions</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/users/create" class="flex items-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all font-medium text-sm">
                <i data-lucide="plus" class="size-4"></i> Add User
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl"><i data-lucide="users" class="size-6"></i></div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Users</p>
                <h3 class="text-2xl font-black text-slate-900"><?= $stats['total'] ?? 0 ?></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl"><i data-lucide="shield-check" class="size-6"></i></div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Active Users</p>
                <h3 class="text-2xl font-black text-slate-900"><?= $stats['active'] ?? 0 ?></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl"><i data-lucide="shield" class="size-6"></i></div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Admins</p>
                <h3 class="text-2xl font-black text-slate-900"><?= $stats['admins'] ?? 0 ?></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl"><i data-lucide="user-check" class="size-6"></i></div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Staff</p>
                <h3 class="text-2xl font-black text-slate-900"><?= $stats['staff'] ?? 0 ?></h3>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-slate-50 bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">User Details</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Role</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (!empty($users)): foreach ($users as $user): ?>
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                                        <p class="text-xs text-slate-400"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider <?= strtolower($user['role_name'] ?? '') === 'admin' ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600' ?>">
                                    <?= htmlspecialchars($user['role_name'] ?? 'Staff') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600">
                                    <span class="size-1.5 rounded-full bg-emerald-600"></span>
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/users/edit/<?= $user['id'] ?>" class="p-2 text-slate-400 hover:text-blue-600 transition-all"><i data-lucide="pencil" class="size-4"></i></a>
                                    <form action="/users/delete/<?= $user['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Delete user?')">
                                        <button class="p-2 text-slate-400 hover:text-rose-600 transition-all"><i data-lucide="trash-2" class="size-4"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center text-slate-400 italic">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>