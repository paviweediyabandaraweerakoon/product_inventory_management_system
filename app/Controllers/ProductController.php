<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Requests\ProductRequest;
use App\Core\Env;
use Throwable;

/**
 * Class ProductController
 * Manages product lifecycle with input sanitization and soft-delete handling.
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

    public function index(): void
    {
        try {
            $products = $this->productModel->all();
            $this->view('products/index', [
                'products' => $products,
                'title' => 'Product List'
            ]);
        } catch (Throwable $e) {
            $this->logError('Index', $e);
            $this->view('errors/500');
            exit;
        }
    }

    public function create(): void
    {
        try {
            $categories = $this->categoryModel->all();
            $this->view('products/create', [
                'categories' => $categories
            ]);
        } catch (Throwable $e) {
            $this->logError('Create View', $e);
            $this->view('errors/500');
            exit;
        }
    }

    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            // Input Handling: Trim and sanitize all inputs
            $data = array_map(fn($v) => trim((string)$v), $_POST);
            $request = new ProductRequest($data);

            if (!$request->validate()) {
                $this->view('products/create', [
                    'errors' => $request->getErrors(),
                    'categories' => $this->categoryModel->all(),
                    'old' => $data
                ]);
                return;
            }

            $this->productModel->create([
                'product_name'   => $data['product_name'],
                'category_id'    => $data['category_id'],
                'price'          => $data['price'],
                'stock_quantity' => $data['stock_quantity'],
                'status'         => 'active'
            ]);

            $this->redirect('/products?success=created');

        } catch (Throwable $e) {
            $this->logError('Store', $e);
            $this->view('errors/500');
            exit;
        }
    }

    public function destroy(string $id): void
    {
        try {
            $this->productModel->delete((int)$id);
            $this->redirect('/products?success=deleted');
        } catch (Throwable $e) {
            $this->logError('Delete', $e);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Helper for URL redirection with base URL handling from .env configuration
     */
    private function redirect(string $path): void
    {
        $baseUrl = rtrim(Env::get('APP_URL'), '/');
        header('Location: ' . $baseUrl . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Standardized error logging
     */
    private function logError(string $action, Throwable $e): void
    {
        error_log(sprintf(
            "[%s] ProductController %s Error: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $action,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));
    }
}