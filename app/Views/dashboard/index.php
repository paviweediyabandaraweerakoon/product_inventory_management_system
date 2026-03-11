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
        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md transition-all hover:shadow-xl border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">1,234</p>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-green-600">
                            <i data-lucide="arrow-up-right" class="size-3"></i>
                            <span class="text-xs font-semibold">+12.5%</span>
                        </div>
                        <span class="text-xs text-gray-500">vs last month</span>
                    </div>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="package" class="size-7"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md transition-all hover:shadow-xl border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Low Stock Alert</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">23</p>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-red-600">
                            <i data-lucide="arrow-down-right" class="size-3"></i>
                            <span class="text-xs font-semibold">-8.2%</span>
                        </div>
                        <span class="text-xs text-gray-500">Action required</span>
                    </div>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-orange-50 text-orange-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="alert-circle" class="size-7"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md transition-all hover:shadow-xl border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inventory Value</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">$45,678</p>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-green-600">
                            <i data-lucide="arrow-up-right" class="size-3"></i>
                            <span class="text-xs font-semibold">+23.1%</span>
                        </div>
                    </div>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-green-50 text-green-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="dollar-sign" class="size-7"></i>
                </div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-md transition-all hover:shadow-xl border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Categories</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">48</p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-gray-500">Across 5 Departments</span>
                    </div>
                </div>
                <div class="flex size-14 items-center justify-center rounded-xl bg-purple-50 text-purple-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="layers" class="size-7"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl bg-white shadow-md border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b p-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Products</h3>
                <a href="/products" class="flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                    View all <i data-lucide="arrow-up-right" class="size-4"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Product</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Stock</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Price</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">Wireless Headphones</div>
                                <div class="text-xs text-gray-500">WH-2024-001</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-semibold">45</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">$89.99</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">In Stock</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-md border border-gray-100">
            <h3 class="mb-6 text-lg font-semibold text-gray-900">Recent Activity</h3>
            <div class="space-y-6">
                <div class="flex gap-3">
                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <i data-lucide="plus" class="size-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Product Added</p>
                        <p class="text-xs text-gray-600">Wireless Mouse by John Doe</p>
                        <p class="mt-1 text-xs text-gray-400">10 minutes ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>