<?php foreach($products as $p): ?>
<tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
    <td class="px-6 py-4">
        <div class="flex items-center gap-3">
            <img src="/assets/images/products/<?= $p['image_path'] ?>" class="w-12 h-12 rounded-lg object-cover">
            <div>
                <p class="font-bold text-gray-900"><?= htmlspecialchars($p['product_name']) ?></p>
                <p class="text-xs text-gray-400"><?= $p['category_name'] ?? 'No Category' ?></p>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 font-mono text-sm"><?= $p['sku'] ?></td>
    <td class="px-6 py-4 font-bold text-blue-600">$<?= number_format($p['price'], 2) ?></td>
    <td class="px-6 py-4">
        <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?= $p['stock_quantity'] < 10 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?>">
            <?= $p['stock_quantity'] ?> in stock
        </span>
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex justify-end gap-2">
            <a href="/products/edit/<?= $p['id'] ?>" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg"><i data-lucide="edit"></i></a>
            <a href="/products/delete/<?= $p['id'] ?>" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" onclick="return confirm('Delete this product?')"><i data-lucide="trash-2"></i></a>
        </div>
    </td>
</tr>
<?php endforeach; ?>