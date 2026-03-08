<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Requests\ProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $productModel = new Product();
        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $total = $productModel->getCount($search);

        return $this->view('products/index', [
            'products' => $productModel->getAll($limit, $offset, $search),
            'totalProducts' => $total,
            'currentPage' => $page,
            'totalPages' => ceil($total / $limit),
            'search' => $search
        ]);
    }

    public function create()
    {
        return $this->view('products/create', [
            'categories' => (new Category())->all()
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            return;
        }

        $validation = new ProductRequest($_POST, $_FILES);
        $errors = $validation->validate() ? [] : $validation->getErrors();
        $productModel = new Product();
        $transactionModel = new InventoryTransaction();
        $db = $productModel->getConnection();

        $imagePath = 'default-prod.png';
        if (empty($errors) && !empty($_FILES['image']['name'])) {
            $imagePath = $this->handleUpload($_FILES['image']);
        }

        if (empty($errors)) {
            try {
                $db->beginTransaction(); 
                $productId = $productModel->create([
                    'product_name'   => $_POST['product_name'],
                    'sku'            => !empty($_POST['sku']) ? $_POST['sku'] : $productModel->generateSKU(),
                    'description'    => $_POST['description'] ?? '',
                    'category_id'    => $_POST['category_id'],
                    'price'          => $_POST['price'],
                    'stock_quantity' => $_POST['stock_quantity'],
                    'low_stock_threshold' => 5,
                    'status'         => $_POST['status'] ?? 'Active',
                    'image_path'     => $imagePath,
                    'created_by'     => $_SESSION['user_id'] ?? 1,
                    'created_at'     => date('Y-m-d H:i:s')
                ]);

                if ($productId && $_POST['stock_quantity'] > 0) {
                    $transactionModel->create([
                        'product_id' => $productId,
                        'transaction_type' => 'IN',
                        'quantity' => $_POST['stock_quantity'],
                        'unit_price' => $_POST['price'],
                        'reason' => 'Initial Stock Creation',
                        'user_id' => $_SESSION['user_id'] ?? 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $db->commit();
                header('Location: /products?success=1');
                exit;
            } catch (\Exception $e) {
                $db->rollBack();
                $errors['db'] = "System Error: " . $e->getMessage();
            }
        }

        return $this->view('products/create', [
            'errors' => $errors, 'old' => $_POST, 'categories' => (new Category())->all()
        ]);
    }

    // --- මම අලුතින් ඇතුළත් කළ කොටස: EDIT ---
    public function edit($id)
    {
        $productModel = new Product();
        $product = $productModel->find($id); // Model එකේ find method එක තිබිය යුතුයි

        if (!$product) {
            header('Location: /products?error=not_found');
            exit;
        }

        return $this->view('products/edit', [
            'product' => $product,
            'categories' => (new Category())->all()
        ]);
    }

    // --- මම අලුතින් ඇතුළත් කළ කොටස: UPDATE ---
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            return;
        }

        $productModel = new Product();
        $oldProduct = $productModel->find($id);

        if (!$oldProduct) {
            header('Location: /products?error=not_found');
            exit;
        }

        // පින්තූරය upload කරන එක check කරන්න
        $imagePath = $oldProduct['image_path'];
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->handleUpload($_FILES['image']);
        }

        $productModel->update($id, [
            'product_name'   => $_POST['product_name'],
            'description'    => $_POST['description'] ?? '',
            'category_id'    => $_POST['category_id'],
            'price'          => $_POST['price'],
            'stock_quantity' => $_POST['stock_quantity'],
            'status'         => $_POST['status'] ?? 'Active',
            'image_path'     => $imagePath,
            'updated_at'     => date('Y-m-d H:i:s')
        ]);

        header('Location: /products?updated=1');
        exit;
    }

    // --- මම අලුතින් ඇතුළත් කළ කොටස: DELETE ---
    public function delete($id)
    {
        $productModel = new Product();
        $productModel->delete($id);
        header('Location: /products?deleted=1');
        exit;
    }

    private function handleUpload($file)
    {
        if (empty($file['name'])) return 'default-prod.png';

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = uniqid('PROD_', true) . '.' . $ext;
        
        $targetDir = dirname(__DIR__, 2) . '/public/uploads/products/';
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $targetDir . $newName)) {
            return $newName;
        }
        return 'default-prod.png';
    }
}