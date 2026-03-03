<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8 flex items-center gap-4">
        <a href="/products" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
            <p class="text-gray-500">Create a new entry in your inventory</p>
        </div>
    </div>

    <form action="/products/store" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        
        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Product Name *</label>
            <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">SKU Code *</label>
            <input type="text" name="sku" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none">
        </div>

        <div class="col-span-2 space-y-2">
            <label class="text-sm font-semibold text-gray-700">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none"></textarea>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Price ($) *</label>
            <input type="number" step="0.01" name="price" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Quantity *</label>
            <input type="number" name="quantity" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Status</label>
            <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 outline-none appearance-none">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-gray-700">Product Image</label>
            <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-2"></i>
                        <p class="text-xs text-gray-500">Click to upload PNG or JPG</p>
                    </div>
                    <input type="file" name="image" class="hidden" accept="image/*" />
                </label>
            </div>
        </div>

        <div class="col-span-2 pt-4">
            <button type="submit" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition-all shadow-lg">
                Save Product
            </button>
        </div>
    </form>
</div>
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>