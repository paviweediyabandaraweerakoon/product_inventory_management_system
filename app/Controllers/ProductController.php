<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Requests\ProductRequest;
use Throwable;

/**
 * Class ProductController
 * Handles CRUD operations for Products.
 */
class ProductController extends Controller
{
    /** @var Product */
    private Product $productModel;

    /** @var Category */
    private Category $categoryModel;

    /**
     * ProductController constructor.
     * Initializes models as controller properties.
     */
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * Show all products.
     * * @return void
     */
    public function index(): void
    {
        try {
            $products = $this->productModel->all();

            $this->view('products/index', [
                'products' => $products,
                'title' => 'Product List'
            ]);
        } catch (Throwable $e) {
            error_log("Product Index Error: " . $e->getMessage());
            $this->view('errors/500');
        }
    }

    /**
     * Show form for adding a new product.
     * * @return void
     */
    public function create(): void
    {
        try {
            $categories = $this->categoryModel->all();

            $this->view('products/create', [
                'categories' => $categories
            ]);
        } catch (Throwable $e) {
            error_log("Product Create View Error: " . $e->getMessage());
            $this->view('errors/500');
        }
    }

    /**
     * Save data from create form to database.
     * * @return void
     */
    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /products');
                return;
            }

            // Sanitizing input data
            $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);
            
            $request = new ProductRequest($data);

            if (!$request->validate()) {
                $this->view('products/create', [
                    'errors' => $request->getErrors(),
                    'categories' => $this->categoryModel->all(),
                    'old' => $data
                ]);
                return;
            }

            // Save the product using validated data
            $this->productModel->create([
                'product_name'   => $data['product_name'],
                'category_id'    => $data['category_id'],
                'price'          => $data['price'],
                'stock_quantity' => $data['stock_quantity'],
                'status'         => 'active'
            ]);

            header('Location: /products?success=created');
            exit;

        } catch (Throwable $e) {
            error_log("Product Store Error: " . $e->getMessage());
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Soft delete a product.
     * * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        try {
            $this->productModel->delete($id);
            header('Location: /products?success=deleted');
            exit;
        } catch (Throwable $e) {
            error_log("Product Delete Error: " . $e->getMessage());
            $this->view('errors/500');
            exit;
        }
    }
}