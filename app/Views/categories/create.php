<?php 
/**
 * @var array|null $_SESSION['errors'] Server-side validation messages (Captured in CategoryController)
 * @var array|null $_SESSION['old'] Previously submitted data to keep the form populated
 */
ob_start();

// Extract session data and clear them immediately (Flash Messages pattern)
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="max-w-xl mx-auto py-8 px-4">
    <div class="flex items-center gap-4 mb-8">
        <a href="/categories" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-500 hover:text-blue-600 transition-all">
            <i data-lucide="chevron-left" class="size-6"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Category</h1>
            <p class="text-sm text-gray-500">Add a new segment for your inventory</p>
        </div>
    </div>

    <form id="catForm" action="/categories" method="POST" class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 space-y-6">
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Category Name <span class="text-red-500">*</span></label>
            <input type="text" id="catName" name="category_name" 
                   value="<?= htmlspecialchars($old['category_name'] ?? '') ?>"
                   class="w-full rounded-xl border <?= isset($errors['category_name']) ? 'border-red-500' : 'border-gray-200' ?> px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all"
                   placeholder="e.g. Computing Devices">
            
            <?php if (isset($errors['category_name'])): ?>
                <p class="mt-2 text-xs text-red-500 font-medium italic"><?= $errors['category_name'] ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
            <textarea id="catDesc" name="description" rows="4" 
                      class="w-full rounded-xl border <?= isset($errors['description']) ? 'border-red-500' : 'border-gray-200' ?> px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all"
                      placeholder="What kind of products belong here?"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            
            <?php if (isset($errors['description'])): ?>
                <p class="mt-2 text-xs text-red-500 font-medium italic"><?= $errors['description'] ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
            <select id="catStatus" name="status" 
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-white cursor-pointer">
                <option value="active" <?= ($old['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    $("#catForm").validate({
        rules: {
            category_name: {
                required: true,
                minlength: 3,
                maxlength: 50
            },
            description: {
                maxlength: 255
            }
        },
        messages: {
            category_name: {
                required: "Please enter a category name.",
                minlength: "Name must be at least 3 characters long."
            }
        },
        errorElement: 'p',
        errorClass: 'mt-2 text-xs text-red-500 font-medium',
        highlight: function(element) {
            $(element).addClass('border-red-500').removeClass('border-gray-200');
        },
        unhighlight: function(element) {
            $(element).removeClass('border-red-500').addClass('border-gray-200');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>