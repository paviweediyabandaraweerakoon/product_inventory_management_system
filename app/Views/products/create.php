<?php

ob_start();
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/products" class="rounded-xl border border-gray-100 bg-white p-2 shadow-sm transition-colors hover:bg-gray-100">
            <i data-lucide="arrow-left" class="h-6 w-6 text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
            <p class="text-sm font-medium text-gray-500">Create a new entry in your inventory</p>
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

    <form
        action="/products/store"
        method="POST"
        enctype="multipart/form-data"
        id="productForm"
        class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm"
    >
        <div class="grid grid-cols-1 gap-6 p-8 md:grid-cols-2">
            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Product Name *</label>
                <input
                    type="text"
                    name="product_name"
                    value="<?= htmlspecialchars((string) ($old['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    required
                    maxlength="255"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                    placeholder="Enter product title"
                >
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Category *</label>
                <select
                    name="category_id"
                    required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
                    <option value="">Select Category</option>
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= (isset($old['category_id']) && (int) $old['category_id'] === (int) $category['id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars((string) $category['category_name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">SKU Code (Optional)</label>
                <input
                    type="text"
                    name="sku"
                    value="<?= htmlspecialchars((string) ($old['sku'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    maxlength="100"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 font-mono text-sm outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                    placeholder="Leave empty for auto-generated SKU"
                >
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Price *</label>
                <input
                    type="number"
                    name="price"
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars((string) ($old['price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Stock Quantity *</label>
                <input
                    type="number"
                    name="stock_quantity"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars((string) ($old['stock_quantity'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Low Stock Threshold *</label>
                <input
                    type="number"
                    name="low_stock_threshold"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars((string) ($old['low_stock_threshold'] ?? '10'), ENT_QUOTES, 'UTF-8') ?>"
                    required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Status *</label>
                <select
                    name="status"
                    required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
                    <option value="active" <?= (($old['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (($old['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Description</label>
                <textarea
                    name="description"
                    rows="4"
                    maxlength="2000"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                ><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Product Image</label>
                <input
                    type="file"
                    name="image"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                >
                <p class="text-xs text-gray-400">Allowed: JPG, JPEG, PNG. Max size: 2 MB.</p>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50/50 p-8">
            <a href="/products" class="rounded-xl px-6 py-3 text-sm font-bold text-gray-600 transition-all hover:bg-gray-200">Cancel</a>
            <button type="submit" class="rounded-xl bg-blue-600 px-10 py-3 font-bold text-white shadow-lg transition-all hover:bg-blue-700">
                Save Product
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('productForm')?.addEventListener('submit', function (event) {
    const productName = document.querySelector('[name="product_name"]')?.value.trim() || '';
    const categoryId = document.querySelector('[name="category_id"]')?.value || '';
    const price = parseFloat(document.querySelector('[name="price"]')?.value || '-1');
    const stockQuantity = parseInt(document.querySelector('[name="stock_quantity"]')?.value || '-1', 10);
    const lowStockThreshold = parseInt(document.querySelector('[name="low_stock_threshold"]')?.value || '-1', 10);
    const imageInput = document.querySelector('[name="image"]');
    const imageFile = imageInput?.files?.[0];

    if (!productName) {
        alert('Product name is required.');
        event.preventDefault();
        return;
    }

    if (!categoryId) {
        alert('Please select a category.');
        event.preventDefault();
        return;
    }

    if (isNaN(price) || price < 0) {
        alert('Please enter a valid non-negative price.');
        event.preventDefault();
        return;
    }

    if (isNaN(stockQuantity) || stockQuantity < 0) {
        alert('Stock quantity must be a non-negative whole number.');
        event.preventDefault();
        return;
    }

    if (isNaN(lowStockThreshold) || lowStockThreshold < 0) {
        alert('Low stock threshold must be a non-negative whole number.');
        event.preventDefault();
        return;
    }

    if (imageFile) {
        const allowedTypes = ['image/jpeg', 'image/png'];
        const maxSize = 2 * 1024 * 1024;

        if (!allowedTypes.includes(imageFile.type)) {
            alert('Only JPG, JPEG, and PNG images are allowed.');
            event.preventDefault();
            return;
        }

        if (imageFile.size > maxSize) {
            alert('Image size must be less than or equal to 2 MB.');
            event.preventDefault();
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>