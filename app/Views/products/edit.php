<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/products" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                <i data-lucide="arrow-left" class="w-6 h-6 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
                <p class="text-sm text-gray-500">Update details for SKU: <span class="font-mono font-bold text-blue-600"><?= $product['sku'] ?></span></p>
            </div>
        </div>
    </div>

    <form action="/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="col-span-2 space-y-2">
                <label class="text-sm font-semibold text-gray-700">Product Name *</label>
                <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">Category</label>
                <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500/20">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">Price ($) *</label>
                <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">SKU (System Generated)</label>
                <input type="text" value="<?= $product['sku'] ?>" readonly 
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-gray-400 font-mono cursor-not-allowed">
            </div>

            <div class="col-span-2 space-y-2">
                <label class="text-sm font-semibold text-gray-700">Description</label>
                <textarea name="description" rows="3" 
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div class="col-span-2 py-4 border-t border-gray-50 mt-4">
                <label class="text-sm font-semibold text-gray-700 block mb-4">Product Image</label>
                <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <img src="/public/uploads/products/<?= $product['image_path'] ?>" class="w-24 h-24 rounded-xl object-cover shadow-sm bg-white border border-gray-100">
                    <div class="space-y-1">
                        <input type="file" name="image" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-400">Current: <?= $product['image_path'] ?>. Upload new to change.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
            <a href="/products" class="px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-200 rounded-xl transition-all">
                Cancel
            </a>
            <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                Update Product
            </button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>