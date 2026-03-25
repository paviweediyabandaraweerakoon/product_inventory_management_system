<?php

ob_start();

$currency = \App\Core\Env::get('APP_CURRENCY', 'LKR');
$imagePath = !empty($product['image_path'])
    ? '/uploads/products/' . rawurlencode((string) $product['image_path'])
    : '/assets/images/product-placeholder.png';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/products" class="rounded-lg p-2 transition-colors hover:bg-gray-100">
                <i data-lucide="arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
        </div>
        <div class="flex gap-3">
            <a href="/products/edit/<?= (int) $product['id'] ?>" class="flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 font-medium text-blue-600">
                <i data-lucide="edit-3" class="h-4 w-4"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="grid gap-8 md:grid-cols-2">
                    <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) ($product['product_name'] ?? 'Product image'), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border object-cover">

                    <div class="space-y-4">
                        <h2 class="text-2xl font-bold"><?= htmlspecialchars((string) ($product['product_name'] ?? 'Unnamed Product'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars((string) ($product['description'] ?? 'No description available.'), ENT_QUOTES, 'UTF-8') ?></p>

                        <div class="space-y-3">
                            <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-3">
                                <i data-lucide="tag" class="text-blue-500"></i>
                                <div>
                                    <p class="text-xs uppercase text-gray-400">SKU Code</p>
                                    <p class="font-mono font-bold"><?= htmlspecialchars((string) ($product['sku'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-3">
                                <i data-lucide="layers" class="text-indigo-500"></i>
                                <div>
                                    <p class="text-xs uppercase text-gray-400">Category</p>
                                    <p class="font-semibold text-gray-900"><?= htmlspecialchars((string) ($product['category_name'] ?? 'Uncategorized'), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-3">
                                <i data-lucide="badge-dollar-sign" class="text-green-500"></i>
                                <div>
                                    <p class="text-xs uppercase text-gray-400">Price</p>
                                    <p class="text-xl font-black text-gray-900"><?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) ($product['price'] ?? 0), 2) ?></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-3">
                                <i data-lucide="circle-check-big" class="text-emerald-500"></i>
                                <div>
                                    <p class="text-xs uppercase text-gray-400">Status</p>
                                    <p class="font-semibold text-gray-900"><?= htmlspecialchars(ucfirst((string) ($product['status'] ?? 'inactive')), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 p-6 text-white shadow-lg">
                <h3 class="mb-4 font-bold">Stock Overview</h3>
                <div class="space-y-2">
                    <p class="text-sm text-indigo-100">Available Quantity</p>
                    <p class="text-4xl font-black"><?= (int) ($product['stock_quantity'] ?? 0) ?></p>
                    <p class="inline-block rounded-full bg-white/20 px-3 py-1 text-sm">
                        <?= ((int) ($product['stock_quantity'] ?? 0) < (int) \App\Core\Env::get('LOW_STOCK_THRESHOLD', 10)) ? 'Low Stock' : 'Healthy Stock' ?>
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-bold text-gray-900">Audit Info</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <div>
                        <p class="text-xs uppercase text-gray-400">Created At</p>
                        <p class="font-medium"><?= htmlspecialchars((string) ($product['created_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400">Updated At</p>
                        <p class="font-medium"><?= htmlspecialchars((string) ($product['updated_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';