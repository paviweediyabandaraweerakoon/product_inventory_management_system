<?php

ob_start();

$currency = \App\Core\Env::get('APP_CURRENCY', 'LKR');
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Product Inventory</h1>
            <p class="text-sm text-gray-500">Manage your products, stock levels and pricing</p>
        </div>

        <a href="/products/create" class="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white shadow-lg shadow-blue-100 transition-all hover:bg-blue-700">
            <i data-lucide="plus" class="h-5 w-5"></i>
            Add Product
        </a>
    </div>

    <div class="flex flex-col items-center justify-between gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm md:flex-row">
        <form action="/products" method="GET" class="relative w-full md:w-96">
            <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Search by product name or SKU..."
                class="w-full rounded-xl border border-gray-200 py-2 pl-10 pr-4 outline-none transition-all focus:ring-2 focus:ring-blue-500/20"
            >
        </form>

        <div class="text-sm text-gray-500">
            Showing <b><?= count($products ?? []) ?></b> of <b><?= (int) ($totalProducts ?? 0) ?></b> products
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Product</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">SKU</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Stock</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $stockQuantity = (int) ($product['stock_quantity'] ?? 0);
                            $threshold = (int) ($product['low_stock_threshold'] ?? 0);
                            $isLowStock = $stockQuantity <= $threshold;
                            $imageUrl = !empty($product['image_path'])
                                ? '/uploads/products/' . rawurlencode((string) $product['image_path'])
                                : '/assets/images/product-placeholder.png';
                            ?>
                            <tr class="transition-colors hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 overflow-hidden rounded-lg border border-gray-100 bg-gray-50">
                                            <img
                                                src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
                                                alt="<?= htmlspecialchars((string) ($product['product_name'] ?? 'Product image'), ENT_QUOTES, 'UTF-8') ?>"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">
                                                <?= htmlspecialchars((string) ($product['product_name'] ?? 'Unnamed Product'), ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                <?= htmlspecialchars((string) ($product['category_name'] ?? 'Uncategorized'), ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-mono text-xs font-bold text-blue-600">
                                    <?= htmlspecialchars((string) ($product['sku'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?php $isActive = (($product['status'] ?? 'inactive') === 'active'); ?>
                                    <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold <?= $isActive ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-600' ?>">
                                        <?= $isActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-bold text-gray-900">
                                    <?= htmlspecialchars((string) $currency, ENT_QUOTES, 'UTF-8') ?>
                                    <?= number_format((float) ($product['price'] ?? 0), 2) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold <?= $isLowStock ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?>">
                                        <span class="h-1.5 w-1.5 rounded-full <?= $isLowStock ? 'bg-red-600' : 'bg-green-600' ?>"></span>
                                        <?= $stockQuantity ?> in stock
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="/products/show/<?= (int) ($product['id'] ?? 0) ?>" class="rounded-lg border border-transparent p-2 text-slate-600 transition-colors hover:border-slate-100 hover:bg-slate-50">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                        </a>

                                        <a href="/products/edit/<?= (int) ($product['id'] ?? 0) ?>" class="rounded-lg border border-transparent p-2 text-blue-600 transition-colors hover:border-blue-100 hover:bg-blue-50">
                                            <i data-lucide="pencil" class="h-4 w-4"></i>
                                        </a>

                                        <form action="/products/delete/<?= (int) ($product['id'] ?? 0) ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')" class="inline">
                                            <button type="submit" class="rounded-lg border border-transparent p-2 text-red-600 transition-colors hover:border-red-100 hover:bg-red-50">
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                No products found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">
                Page <b><?= (int) ($currentPage ?? 1) ?></b> of <b><?= (int) ($totalPages ?? 1) ?></b>
            </p>

            <div class="flex items-center gap-2">
                <?php if (($currentPage ?? 1) > 1): ?>
                    <a
                        href="/products?search=<?= urlencode((string) ($search ?? '')) ?>&page=<?= (int) (($currentPage ?? 1) - 1) ?>"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Previous
                    </a>
                <?php endif; ?>

                <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
                    <a
                        href="/products?search=<?= urlencode((string) ($search ?? '')) ?>&page=<?= (int) (($currentPage ?? 1) + 1) ?>"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>