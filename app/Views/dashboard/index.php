<?php include __DIR__ . '/../layouts/header.php'; ?>

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
        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900"><?= number_format($totalProducts) ?></p>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i data-lucide="package" class="size-7"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Low Stock Alert</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900"><?= $lowStockCount ?></p>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                    <i data-lucide="alert-triangle" class="size-7"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inventory Value</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">$0.00</p>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-green-50 text-green-600">
                    <i data-lucide="dollar-sign" class="size-7"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Categories</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900"><?= $activeCategories ?></p>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                    <i data-lucide="layers" class="size-7"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Sales Performance</h3>
            <div class="relative h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Category Distribution</h3>
            <div class="relative h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl bg-white shadow-md border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b p-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Products</h3>
                <a href="/products" class="text-sm font-medium text-blue-600">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Product</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Stock</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($recentProducts as $product): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($product['product_name']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= $product['stock_quantity'] ?></td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $product['stock_quantity'] > 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $product['stock_quantity'] > 5 ? 'In Stock' : 'Low Stock' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <h3 class="mb-6 text-lg font-semibold text-gray-900">Recent Activity</h3>
            <div class="flex gap-3">
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <i data-lucide="check" class="size-4"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">System Ready</p>
                    <p class="text-xs text-gray-600">Dashboard UI updated with Charts.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Category Donut Chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                data: <?= json_encode($chartData) ?>,
                backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
    });

    // Sales Line Chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Monthly Sales',
                data: [3000, 2500, 4200, 3800, 5000, 4800],
                borderColor: '#4F46E5',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(79, 70, 229, 0.05)'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>