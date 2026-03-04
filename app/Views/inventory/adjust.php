<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-lg mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Adjust Stock: <?= htmlspecialchars($product['product_name']) ?></h1>
    
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
        <p class="mb-4 text-gray-600">Current Stock: <span class="font-bold text-blue-600"><?= $product['stock_quantity'] ?></span></p>

        <form action="/inventory/update" method="POST" class="space-y-4">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <div>
                <label class="block text-sm font-bold mb-2">Transaction Type</label>
                <select name="transaction_type" class="w-full border rounded-xl p-3">
                    <option value="IN">Stock IN (+)</option>
                    <option value="OUT">Stock OUT (-)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Quantity</label>
                <input type="number" name="quantity" min="1" required class="w-full border rounded-xl p-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Reason / Note</label>
                <textarea name="reason" class="w-full border rounded-xl p-3" placeholder="Restock, Sale, Damage etc."></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">
                Update Inventory
            </button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>