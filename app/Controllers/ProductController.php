<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Requests\ProductRequest;

class ProductController extends Controller
{
    public function index() {
        $productModel = new Product();
        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 5;
        $offset = ($page - 1) * $limit;

        return $this->view('products/index', [
            'products' => $productModel->getAll($limit, $offset, $search),
            'total' => $productModel->getCount($search),
            'currentPage' => $page,
            'totalPages' => ceil($productModel->getCount($search) / $limit),
            'search' => $search
        ]);
    }

    public function create() {
        $categoryModel = new Category();
        return $this->view('products/create', ['categories' => $categoryModel->all()]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            return;
        }

        // 1. Validation 
        $validation = new ProductRequest($_POST);
        $errors = [];
        if (!$validation->validate()) {
            $errors = $validation->getErrors();
        }

        $productModel = new Product();
        $transactionModel = new InventoryTransaction();

        // 2. Secure Image Upload 
        $imagePath = 'default-prod.png';
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // MIME Type Validation
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (in_array($ext, ['jpg', 'jpeg', 'png']) && strpos($mime, 'image/') === 0 && $file['size'] < 5000000) {
                $imagePath = uniqid('PROD_', true) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], "public/uploads/products/" . $imagePath);
            } else {
                $errors['image'] = "Invalid image (Only JPG/PNG, max 5MB)";
            }
        }

        if (empty($errors)) {
            try {
                $db = $productModel->getDb();
                $db->beginTransaction();

                // 3. Insert Product
                $productId = $productModel->insert([
                    'product_name'   => $_POST['product_name'],
                    'sku'            => !empty($_POST['sku']) ? $_POST['sku'] : $productModel->generateSKU($_POST['category_id']),
                    'description'    => $_POST['description'] ?? '',
                    'category_id'    => $_POST['category_id'],
                    'price'          => $_POST['price'],
                    'stock_quantity' => $_POST['stock_quantity'],
                    'status'         => $_POST['status'] ?? 'active',
                    'image_path'     => $imagePath,
                    'created_by'     => $_SESSION['user_id'] ?? 1
                ]);

                // 4. Log Transaction (For Chart & History)
                if ($productId && (int)$_POST['stock_quantity'] > 0) {
                    $transactionModel->insert([
                        'product_id'       => $productId,
                        'transaction_type' => 'IN',
                        'quantity'         => $_POST['stock_quantity'],
                        'reason'           => 'Initial Stock Entry',
                        'user_id'          => $_SESSION['user_id'] ?? 1
                    ]);
                }

                $db->commit();
                header('Location: /products?success=1');
                exit;

            } catch (\Exception $e) {
                if (isset($db)) $db->rollBack();
                $errors['db'] = "Failed to save: " . $e->getMessage();
            }
        }

        $categoryModel = new Category();
        return $this->view('products/create', [
            'errors' => $errors,
            'old' => $_POST,
            'categories' => $categoryModel->all()
        ]);
    }

    public function delete($id) {
        (new Product())->deleteProduct($id);
        header('Location: /products?deleted=1');
        exit;
    }
}