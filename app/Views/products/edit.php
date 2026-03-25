<?php

ob_start();
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/products" class="rounded-xl border border-gray-100 bg-white p-2 shadow-sm transition-colors hover:bg-gray-100">
            <i data-lucide="arrow-left" class="h-6 w-6 text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
            <p class="text-sm font-medium text-gray-500">
                Update details for SKU:
                <span class="font-mono font-bold text-blue-600"><?= htmlspecialchars((string) ($product['sku'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
            </p>
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
        action="/products/update/<?= (int) ($product['id'] ?? 0) ?>"
        method="POST"
        enctype="multipart/form-data"
        id="productEditForm"
        class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm"
    >
        <div class="grid grid-cols-1 gap-6 p-8 md:grid-cols-2">
            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Product Name *</label>
                <input
                    type="text"
                    name="product_name"
                    value="<?= htmlspecialchars((string) ($product['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    required
                    maxlength="255"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Category *</label>
                <select
                    name="category_id"
                    required
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= ((int) ($category['id'] ?? 0) === (int) ($product['category_id'] ?? 0)) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars((string) $category['category_name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">SKU Code *</label>
                <input
                    type="text"
                    name="sku"
                    value="<?= htmlspecialchars((string) ($product['sku'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    required
                    maxlength="100"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 font-mono text-sm outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                >
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Price *</label>
                <input
                    type="number"
                    name="price"
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars((string) ($product['price'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars((string) ($product['stock_quantity'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars((string) ($product['low_stock_threshold'] ?? '10'), ENT_QUOTES, 'UTF-8') ?>"
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
                    <option value="active" <?= (($product['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (($product['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Description</label>
                <textarea
                    name="description"
                    rows="4"
                    maxlength="2000"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                ><?= htmlspecialchars((string) ($product['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="col-span-2 space-y-3 border-t border-gray-100 pt-4">
                <label class="text-sm font-bold text-gray-700">Product Image</label>

                <div class="flex flex-col gap-4 rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4 md:flex-row md:items-center">
                    <div class="h-24 w-24 overflow-hidden rounded-xl border border-gray-100 bg-white">
                        <?php if (!empty($product['image_path'])): ?>
                            <img
                                src="/uploads/products/<?= rawurlencode((string) $product['image_path']) ?>"
                                alt="<?= htmlspecialchars((string) ($product['product_name'] ?? 'Product image'), ENT_QUOTES, 'UTF-8') ?>"
                                class="h-full w-full object-cover"
                            >
                        <?php else: ?>
                            <div class="flex h-full w-full items-center justify-center text-xs text-gray-400">No image</div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-1">
                        <input
                            type="file"
                            name="image"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            class="text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                        >
                        <p class="text-xs text-gray-400">Upload a new image only if you want to replace the current one.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50/50 p-8">
            <a href="/products" class="rounded-xl px-6 py-3 text-sm font-bold text-gray-600 transition-all hover:bg-gray-200">Cancel</a>
            <button type="submit" class="rounded-xl bg-blue-600 px-10 py-3 font-bold text-white shadow-lg transition-all hover:bg-blue-700">
                Update Product
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('productEditForm')?.addEventListener('submit', function (event) {
    const productName = document.querySelector('[name="product_name"]')?.value.trim() || '';
    const categoryId = document.querySelector('[name="category_id"]')?.value || '';
    const sku = document.querySelector('[name="sku"]')?.value.trim() || '';
    const price = parseFloat(document.querySelector('[name="price"]')?.value || '-1');
    const stockQuantity = parseInt(document.querySelector('[name="stock_quantity"]')?.value || '-1', 10);
    const lowStockThreshold = parseInt(document.querySelector('[name="low_stock_threshold"]')?.value || '-1', 10);
    const imageInput = document.querySelector('[name="image"]');
    const imageFile = imageInput?.files?.[0];

    if (!productName || !categoryId || !sku) {
        alert('Product name, category, and SKU are required.');
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