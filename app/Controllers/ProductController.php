<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller {
    
    public function index() {
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
            'totalPages' => ceil($total / $limit)
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Server-side validation
            if(empty($_POST['product_name']) || empty($_POST['sku'])) {
                die("Required fields missing");
            }

            $imagePath = 'default-prod.png';
            if (!empty($_FILES['image']['name'])) {
                $file = $_FILES['image'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png'];
                
                // Requirement 6: Validate MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (in_array($ext, $allowed) && strpos($mime, 'image/') === 0) {
                    // Requirement 6: Unique generated name
                    $imagePath = uniqid('PROD_', true) . '.' . $ext;
                    move_uploaded_file($file['tmp_name'], "public/assets/images/products/" . $imagePath);
                }
            }

            $productModel = new Product();
            $productModel->create([
                'product_name' => $_POST['product_name'],
                'sku'          => $_POST['sku'],
                'description'  => $_POST['description'],
                'price'        => $_POST['price'],
                'stock_quantity' => $_POST['stock_quantity'],
                'image_path'   => $imagePath,
                'category_id'  => $_POST['category_id'] ?? null
            ]);

            header('Location: /products');
        }
    }
}