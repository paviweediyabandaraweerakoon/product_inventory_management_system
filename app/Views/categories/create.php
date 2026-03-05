<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="max-w-xl mx-auto py-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="/categories" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-500 hover:text-blue-600 transition-all">
            <i data-lucide="chevron-left" class="size-6"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Category</h1>
            <p class="text-sm text-gray-500">Add a new segment for your inventory</p>
        </div>
    </div>

    <form id="catForm" action="/categories/store" method="POST" class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 space-y-6">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Category Name <span class="text-red-500">*</span></label>
            <input type="text" id="catName" name="category_name" required 
                   class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all"
                   placeholder="e.g. Smart Home Devices">
            <p id="nameError" class="mt-2 text-xs text-red-500 hidden font-medium">Please enter a valid category name (min 3 chars).</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
            <textarea id="catDesc" name="description" rows="4" 
                      class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all"
                      placeholder="What kind of products belong here?"></textarea>
            <p id="descError" class="mt-2 text-xs text-red-500 hidden font-medium">Description should be at least 10 characters.</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
            <select id="catStatus" name="status" required 
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-white cursor-pointer">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4">
            <a href="/categories" class="flex items-center justify-center px-6 py-3 border border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition-all">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/25 hover:opacity-90 transition-all">
                Save Category
            </button>
        </div>
    </form>
</div>

<script>
// Client-side Validation 
document.getElementById('catForm').onsubmit = function(e) {
    let valid = true;
    const name = document.getElementById('catName').value;
    const desc = document.getElementById('catDesc').value;

    // Name validation
    if (name.trim().length < 3) {
        document.getElementById('nameError').classList.remove('hidden');
        valid = false;
    } else {
        document.getElementById('nameError').classList.add('hidden');
    }

    // Description validation (Description එක අනිවාර්ය කරලා නැත්නම් විතරක් මේක use කරන්න)
    if (desc.trim().length > 0 && desc.trim().length < 10) {
        document.getElementById('descError').classList.remove('hidden');
        valid = false;
    } else {
        document.getElementById('descError').classList.add('hidden');
    }

    if (!valid) e.preventDefault();
};
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>