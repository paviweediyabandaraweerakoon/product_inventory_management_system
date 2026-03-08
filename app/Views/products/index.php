<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Product Inventory</h1>
            <p class="text-sm text-gray-500">Manage your products, stock levels and pricing</p>
        </div>
        <div class="flex gap-3">
            <a href="/products/create" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                <i data-lucide="plus" class="w-5 h-5"></i> Add Product
            </a>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <form action="/products" method="GET" class="relative w-full md:w-96">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                placeholder="Search by product name or SKU..." 
                class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
        </form>
        <div class="text-sm text-gray-500">
            Showing <b><?= count($products) ?></b> of <b><?= $totalProducts ?></b> products
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Product Info</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Stock Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($products)): ?>
                        <?php foreach($products as $p): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-100 bg-gray-50">
                                        <img src="/uploads/products/<?= $p['image_path'] ?>" 
                                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($p['product_name']) ?>&background=random'"
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900"><?= htmlspecialchars($p['product_name']) ?></p>
                                        <p class="text-xs text-gray-400"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-blue-600 font-bold"><?= $p['sku'] ?></td>
                            <td class="px-6 py-4 font-bold text-gray-900">$<?= number_format((float)$p['price'], 2) ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold <?= $p['stock_quantity'] < 10 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $p['stock_quantity'] < 10 ? 'bg-red-600' : 'bg-green-600' ?>"></span>
                                    <?= $p['stock_quantity'] ?> in stock
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/products/edit/<?= $p['id'] ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-100">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form action="/products/delete/<?= $p['id'] ?>" method="POST" onsubmit="return confirm('Are you sure?')" class="inline">
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-20 text-center text-gray-400">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>