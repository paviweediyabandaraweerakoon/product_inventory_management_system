<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;

class ProductController extends Controller
{
    /**
     * List View with Search & Pagination
     */
    public function index()
    {
        $productModel = new Product();
        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $products = $productModel->getAll($limit, $offset, $search);
        $total = $productModel->getCount($search);

        return $this->view('products/index', [
            'products' => $products,
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => ceil($total / $limit),
            'search' => $search
        ]);
    }

    public function create()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->all();
        return $this->view('products/create', ['categories' => $categories]);
    }

    /**
     * Store Product & Log Transaction
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            return;
        }

        $errors = [];
        $productModel = new Product();
        $transactionModel = new InventoryTransaction();

        // 1. Validation
        if (empty($_POST['product_name']) || strlen($_POST['product_name']) < 3) {
            $errors['product_name'] = "Name is required (min 3 chars)";
        }
        if (floatval($_POST['price'] ?? 0) <= 0) {
            $errors['price'] = "Price must be positive";
        }

        // 2. Secure Image Upload (Requirement 5.2 & 6)
        $imagePath = 'default-prod.png';
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            // MIME Type Validation
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (in_array($ext, $allowed) && strpos($mime, 'image/') === 0 && $file['size'] < 5000000) {
                $uploadDir = "public/uploads/products/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $imagePath = uniqid('PROD_', true) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadDir . $imagePath);
            } else {
                $errors['image'] = "Invalid image format or size (max 5MB)";
            }
        }

        if (empty($errors)) {
            // Insert Product
            $productId = $productModel->insert([
                'product_name'   => $_POST['product_name'],
                'sku'            => !empty($_POST['sku']) ? $_POST['sku'] : $productModel->generateSKU($_POST['category_id']),
                'description'    => $_POST['description'] ?? '',
                'category_id'    => $_POST['category_id'],
                'price'          => $_POST['price'],
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'image_path'     => $imagePath,
                'created_by'     => $_SESSION['user_id'] ?? 1
            ]);

            // Requirement 5.3: Inventory Transaction Logging
            if ($productId && ($_POST['stock_quantity'] > 0)) {
                $transactionModel->insert([
                    'product_id'       => $productId,
                    'transaction_type' => 'IN',
                    'quantity'         => $_POST['stock_quantity'],
                    'reason'           => 'Initial Stock Entry',
                    'user_id'          => $_SESSION['user_id'] ?? 1
                ]);
            }

            header('Location: /products');
            exit;
        }

        $categoryModel = new Category();
        return $this->view('products/create', [
            'errors' => $errors, 
            'old' => $_POST, 
            'categories' => $categoryModel->all()
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            return;
        }

        $productModel = new Product();
        $errors = [];

        // fetch existing product to compare
        $existing = $productModel->find($id);
        if (!$existing) {
            header('Location: /products');
            return;
        }

        // validation
        if (empty($_POST['product_name']) || strlen($_POST['product_name']) < 3) {
            $errors['product_name'] = "Name is required (min 3 chars)";
        }
        if (floatval($_POST['price'] ?? 0) <= 0) {
            $errors['price'] = "Price must be positive";
        }

        // image upload if provided
        $imagePath = $existing['image_path'];
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (in_array($ext, $allowed) && strpos($mime, 'image/') === 0 && $file['size'] < 5000000) {
                $uploadDir = "public/uploads/products/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $imagePath = uniqid('PROD_', true) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadDir . $imagePath);
            } else {
                $errors['image'] = "Invalid image format or size (max 5MB)";
            }
        }

        if (empty($errors)) {
            // perform update
            $productModel->updateProduct($id, [
                'product_name'   => $_POST['product_name'],
                'sku'            => $_POST['sku'] ?? $existing['sku'],
                'description'    => $_POST['description'] ?? '',
                'category_id'    => $_POST['category_id'],
                'price'          => $_POST['price'],
                'stock_quantity' => $_POST['stock_quantity'] ?? $existing['stock_quantity'],
                'image_path'     => $imagePath,
                'updated_by'     => $_SESSION['user_id'] ?? 1
            ]);

            header('Location: /products');
            exit;
        }

        $categoryModel = new Category();
        return $this->view('products/edit', [
            'errors' => $errors,
            'old' => $_POST,
            'product' => $existing,
            'categories' => $categoryModel->all()
        ]);
    }

    public function edit($id)
    {
        $productModel = new Product();
        $categoryModel = new Category();
        
        $product = $productModel->find($id);
        if (!$product) {
            header('Location: /products');
            exit;
        }

        return $this->view('products/edit', [
            'product' => $product,
            'categories' => $categoryModel->all()
        ]);
    }

    public function delete($id)
    {
        $productModel = new Product();
        $productModel->deleteProduct($id);
        header('Location: /products?deleted=1');
        exit;
    }
}