<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="/products" class="p-2 hover:bg-gray-100 rounded-xl transition-colors border border-gray-100 bg-white shadow-sm">
            <i data-lucide="arrow-left" class="w-6 h-6 text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
            <p class="text-sm text-gray-500 font-medium">Create a new entry in your inventory</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-2xl text-sm mb-4">
            <ul class="list-disc list-inside font-medium">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/products/store" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Product Name *</label>
                <input type="text" name="product_name" value="<?= htmlspecialchars($old['product_name'] ?? '') ?>" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-400" 
                    placeholder="Enter product title">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Category *</label>
                <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                    <option value="">Select Category</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($old['category_id']) && $old['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">SKU Code (Optional)</label>
                <input type="text" name="sku" value="<?= htmlspecialchars($old['sku'] ?? '') ?>" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono text-sm" 
                    placeholder="Leave empty for auto-gen">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Price ($) *</label>
                <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($old['price'] ?? '') ?>" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Stock Quantity *</label>
                <input type="number" name="stock_quantity" value="<?= htmlspecialchars($old['stock_quantity'] ?? '') ?>" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
            </div>

            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            </div>

            <div class="col-span-2 space-y-2">
                <label class="text-sm font-bold text-gray-700">Product Image</label>
                <input type="file" name="image" class="w-full block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" />
            </div>
        </div>

        <div class="p-8 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-3">
            <a href="/products" class="px-6 py-3 text-sm font-bold text-gray-600 hover:bg-gray-200 rounded-xl transition-all">Cancel</a>
            <button type="submit" class="px-10 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition-all">Save Product</button>
        </div>
    </form>
</div>