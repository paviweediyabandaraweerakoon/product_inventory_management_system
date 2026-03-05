<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8 flex items-center gap-4">
        <a href="/products" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <h1 class="text-2xl font-bold">Add New Product</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            <?php foreach ($errors as $error): ?> <p><?= $error ?></p> <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/products/store" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        
        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Product Name *</label>
            <input type="text" name="product_name" value="<?= $old['product_name'] ?? '' ?>" required class="w-full px-4 py-2.5 rounded-xl border focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Category *</label>
            <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl border outline-none">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (isset($old['category_id']) && $old['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= $cat['category_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">SKU Code (Optional)</label>
            <input type="text" name="sku" value="<?= $old['sku'] ?? '' ?>" placeholder="Auto-gen if empty" class="w-full px-4 py-2.5 rounded-xl border outline-none">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Price ($) *</label>
            <input type="number" step="0.01" name="price" value="<?= $old['price'] ?? '' ?>" required class="w-full px-4 py-2.5 rounded-xl border outline-none">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Initial Stock *</label>
            <input type="number" name="stock_quantity" value="<?= $old['stock_quantity'] ?? '0' ?>" required class="w-full px-4 py-2.5 rounded-xl border outline-none">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Status</label>
            <select name="status" class="w-full px-4 py-2.5 rounded-xl border outline-none">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="col-span-2 space-y-2">
            <label class="text-sm font-semibold text-gray-700">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border outline-none"><?= $old['description'] ?? '' ?></textarea>
        </div>

        <div class="col-span-2 space-y-2">
            <label class="text-sm font-semibold text-gray-700">Product Image</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded-xl p-2">
        </div>

        <div class="col-span-2 pt-4">
            <button type="submit" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 shadow-lg">
                Save Product
            </button>
        </div>
    </form>
</div>
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>