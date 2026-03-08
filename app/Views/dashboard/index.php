<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Overview</h1>
            <p class="mt-1 flex items-center gap-2 text-gray-600">
                <i data-lucide="activity" class="size-4"></i>
                Real-time inventory monitoring and analytics
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50">
                <i data-lucide="download" class="size-4"></i> Export Report
            </button>
            <a href="/products/create" class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 font-medium text-white shadow-lg transition-all hover:shadow-xl hover:scale-105">
                <i data-lucide="plus" class="size-5"></i> Add Product
            </a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900"><?= number_format($totalProducts ?? 0) ?></p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <i data-lucide="package" class="size-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Low Stock Alert</p>
                    <p class="mt-2 text-3xl font-bold text-red-600"><?= $lowStockCount ?? 0 ?></p>
                </div>
                <div class="p-3 bg-red-50 rounded-xl text-red-600">
                    <i data-lucide="alert-triangle" class="size-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Inventory Value</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">$<?= number_format((float)($inventoryValue ?? 0), 2) ?></p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl text-green-600">
                    <i data-lucide="dollar-sign" class="size-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Active Categories</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900"><?= $activeCategories ?? 0 ?></p>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                    <i data-lucide="layers" class="size-6"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Sales Performance</h3>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Category Distribution</h3>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-lg">Recent Products</h3>
            <a href="/products" class="text-blue-600 text-sm font-semibold hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">Stock Status</th>
                        <th class="px-6 py-4">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(!empty($recentProducts)): ?>
                        <?php foreach ($recentProducts as $product): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($product['product_name']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $product['stock_quantity'] > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                    <?= $product['stock_quantity'] ?> in stock
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">$<?= number_format((float)$product['price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>