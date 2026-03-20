<?php
ob_start();
?>
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
        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
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

        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
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

        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inventory Value</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">LKR <?= $inventoryValue ?></p>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-green-50 text-green-600">
                    <i data-lucide="banknote" class="size-7"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
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
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Sales Performance (Last <?= \App\Core\Env::get('DASHBOARD_REPORT_MONTHS', 6) ?> Months)</h3>
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
                <h3 class="text-lg font-semibold text-gray-900">Recent Catalog Additions</h3>
                <a href="/products" class="text-sm font-medium text-blue-600 hover:underline">View all catalog</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Product Name</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Available Stock</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Availability</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($recentProducts)): ?>
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No recent products found.</td></tr>
                        <?php else: foreach ($recentProducts as $product): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($product['product_name']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono"><?= $product['stock_quantity'] ?></td>
                            <td class="px-6 py-4">
                                <?php $isLow = $product['stock_quantity'] <= \App\Core\Env::get('LOW_STOCK_THRESHOLD', 5); ?>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold <?= !$isLow ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= !$isLow ? 'In Stock' : 'Low Stock' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <h3 class="mb-6 text-lg font-semibold text-gray-900">Recent Inventory Movements</h3>
            <div class="space-y-4">
                <?php if (empty($recentActivity)): ?>
                    <p class="text-sm text-gray-500 text-center">No recent activity logged.</p>
                <?php else: foreach ($recentActivity as $activity): ?>
                    <div class="flex gap-3 border-l-2 <?= $activity['transaction_type'] === 'IN' ? 'border-green-500' : 'border-blue-500' ?> pl-3">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full <?= $activity['transaction_type'] === 'IN' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' ?>">
                            <i data-lucide="<?= $activity['transaction_type'] === 'IN' ? 'arrow-up-right' : 'arrow-down-right' ?>" class="size-4"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($activity['product_name']) ?> 
                                <span class="text-xs font-normal text-gray-500">
                                    (<?= $activity['transaction_type'] === 'IN' ? '+' : '-' ?><?= $activity['quantity'] ?>)
                                </span>
                            </p>
                            <p class="text-xs text-gray-600 italic">"<?= htmlspecialchars($activity['remarks'] ?? $activity['reason'] ?? 'Stock Update') ?>"</p>
                            <p class="text-[10px] text-gray-400 mt-1"><?= date('M d, h:i A', strtotime($activity['created_at'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Category Donut Chart (Dynamic)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                data: <?= json_encode($chartData) ?>,
                backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });

    // 2. Sales Performance Line Chart (Dynamic)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($salesLabels) ?>, // Months from DB
            datasets: [{
                label: 'Monthly Revenue (LKR)',
                data: <?= json_encode($salesData) ?>, // Totals from DB
                borderColor: '#4F46E5',
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#4F46E5',
                fill: true,
                backgroundColor: (context) => {
                    const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                    gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
                    return gradient;
                }
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>