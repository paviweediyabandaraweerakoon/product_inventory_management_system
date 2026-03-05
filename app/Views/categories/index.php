<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white/50 p-6 rounded-2xl backdrop-blur-sm border border-white/20 shadow-sm">
        <div>
            <h1 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">Categories</h1>
            <p class="mt-1 text-gray-500 font-medium">Manage and organize your inventory modules</p>
        </div>
        <a href="/categories/create" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 font-bold text-white shadow-lg shadow-blue-500/30 transition-all hover:scale-105 active:scale-95">
            <i data-lucide="plus-circle" class="size-5"></i> New Category
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($categories as $cat): ?>
        <div class="group relative bg-white p-6 rounded-2xl shadow-md border border-gray-100 transition-all hover:shadow-2xl hover:-translate-y-1">
            <div class="flex items-start justify-between mb-6">
                <div class="flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-50 to-purple-50 text-blue-600 group-hover:from-blue-600 group-hover:to-purple-600 group-hover:text-white transition-all duration-300">
                    <i data-lucide="folder-tree" class="size-7"></i>
                </div>
                <div class="flex gap-1">
                    <a href="/categories/edit/<?= $cat['id'] ?>" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                        <i data-lucide="pencil" class="size-4"></i>
                    </a>
                    <a href="/categories/delete/<?= $cat['id'] ?>" onclick="return confirm('Delete this category?')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                        <i data-lucide="trash-2" class="size-4"></i>
                    </a>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                <?= htmlspecialchars($cat['category_name']) ?>
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6 line-clamp-2">
                <?= htmlspecialchars($cat['description']) ?>
            </p>
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                <div class="flex items-center gap-2 px-3 py-1 bg-gray-50 rounded-full text-gray-600">
                    <i data-lucide="package" class="size-3.5"></i>
                    <span class="text-xs font-bold"><?= $cat['productCount'] ?> Products</span>
                </div>
                
                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?= ($cat['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <span class="size-1.5 rounded-full <?= ($cat['status'] ?? 'active') === 'active' ? 'bg-green-500 animate-pulse' : 'bg-red-500' ?>"></span>
                    <?= $cat['status'] ?? 'active' ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>