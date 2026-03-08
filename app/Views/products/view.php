<div class="max-w-6xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/products" class="p-2 hover:bg-gray-100 rounded-lg"><i data-lucide="arrow-left"></i></a>
            <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
        </div>
        <div class="flex gap-3">
            <a href="/products/edit/<?= $product['id'] ?>" class="flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-medium border border-blue-200">
                <i data-lucide="edit-3" class="w-4 h-4"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="grid md:grid-cols-2 gap-8">
                    <img src="/uploads/products/<?= $product['image_path'] ?>" class="w-full rounded-xl border object-cover">
                    
                    <div class="space-y-4">
                        <h2 class="text-2xl font-bold"><?= htmlspecialchars($product['product_name']) ?></h2>
                        <p class="text-gray-500 text-sm"><?= htmlspecialchars($product['description']) ?></p>
                        
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl">
                                <i data-lucide="tag" class="text-blue-500"></i>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase">SKU Code</p>
                                    <p class="font-mono font-bold"><?= $product['sku'] ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl">
                                <i data-lucide="dollar-sign" class="text-green-500"></i>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase">Price</p>
                                    <p class="text-xl font-black text-gray-900">$<?= number_format($product['price'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-6 rounded-2xl text-white shadow-lg">
                <h3 class="font-bold mb-4">Stock Overview</h3>
                <div class="space-y-2">
                    <p class="text-indigo-100 text-sm">Available Quantity</p>
                    <p class="text-4xl font-black"><?= $product['stock_quantity'] ?></p>
                    <p class="text-sm bg-white/20 inline-block px-3 py-1 rounded-full">
                        <?= $product['stock_quantity'] < 10 ? 'Low Stock' : 'Healthy Stock' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>