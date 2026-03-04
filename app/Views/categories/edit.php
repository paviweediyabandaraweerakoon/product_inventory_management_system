<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-xl mx-auto py-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="/categories" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-500 hover:text-blue-600 transition-all">
            <i data-lucide="chevron-left" class="size-6"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
            <p class="text-sm text-gray-500">Update information for: <span class="text-blue-600 font-bold"><?= htmlspecialchars($category['category_name']) ?></span></p>
        </div>
    </div>

    <form id="editCatForm" action="/categories/update/<?= $category['id'] ?>" method="POST" class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 space-y-6">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Category Name <span class="text-red-500">*</span></label>
            <input type="text" id="catName" name="category_name" required 
                   value="<?= htmlspecialchars($category['category_name']) ?>"
                   class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
            <textarea id="catDesc" name="description" rows="4" 
                      class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all"><?= htmlspecialchars($category['description']) ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                <option value="active" <?= $category['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $category['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4">
            <a href="/categories" class="flex items-center justify-center px-6 py-3 border border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition-all">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/25 hover:opacity-90 transition-all">
                Update Category
            </button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>