<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Requests\ProductRequest;
use Throwable;

/**
 * Class ProductController
 * Handles product CRUD operations and management.
 */
class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * List all active products.
     */
    public function index(): void
    {
        try {
            $products = $this->productModel->all();
            $totalProducts = $this->productModel->countActiveRecords();

            $this->view('products/index', [
                'products'      => $products,
                'totalProducts' => $totalProducts,
                'title'         => 'Product List',
            ]);
        } catch (Throwable $e) {
            $this->logAndHandleError('Index', $e);
        }
    }

    /**
     * Show create product form.
     */
    public function create(): void
    {
        try {
            $categories = $this->categoryModel->all() ?: [];
            $this->view('products/create', [
                'categories' => $categories,
                'title'      => 'Add New Product',
            ]);
        } catch (Throwable $e) {
            $this->logAndHandleError('Create', $e);
        }
    }

    /**
     * Store new product after validation.
     */
    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            $data = $this->getTrimmedPostData();
            $request = new ProductRequest($data);

            if (!$request->validate()) {
                $this->view('products/create', [
                    'errors'     => $request->getErrors(),
                    'categories' => $this->categoryModel->all() ?: [],
                    'old'        => $data,
                ]);
                return;
            }

            $imagePath = $this->handleImageUpload();

            $this->productModel->create([
                'product_name'   => $data['product_name'],
                'category_id'    => (int) $data['category_id'],
                'price'          => (float) $data['price'],
                'stock_quantity' => (int) $data['stock_quantity'],
                'image_path'     => $imagePath,
                'status'         => 'active',
            ]);

            $this->redirect('/products?success=created');
        } catch (Throwable $e) {
            $this->logAndHandleError('Store', $e);
        }
    }

    /**
     * Soft delete product.
     */
    public function destroy(string $id): void
    {
        try {
            $product = $this->productModel->findById((int)$id);
            if (!$product) {
                $this->redirect('/products?error=not_found');
                return;
            }

            $this->productModel->delete((int)$id);
            $this->redirect('/products?success=deleted');
        } catch (Throwable $e) {
            $this->logAndHandleError('Destroy', $e);
        }
    }

    /**
     * Reusable error logging.
     */
    private function logAndHandleError(string $context, Throwable $e): void
    {
        error_log("[ProductController@{$context}] " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        http_response_code(500);
        $this->view('errors/500', ['title' => 'Internal Server Error']);
        exit;
    }

    /**
     * Trim POST data.
     */
    private function getTrimmedPostData(): array
    {
        return array_map(fn($v) => is_scalar($v) ? trim((string)$v) : '', $_POST);
    }

    /**
     * Handle secure image upload.
     */
    private function handleImageUpload(): ?string
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                return null;
            }

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newName = uniqid('product_', true) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/uploads/products/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destination = $uploadDir . $newName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                return '/uploads/products/' . $newName;
            }
        }
        return null;
    }
}