<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Requests\ProductRequest;
use App\Services\ProductService;
use Throwable;

/**
 * Class ProductController
 * Manages product lifecycle operations including listing, creation,
 * validation, and soft-delete handling.
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
     * Display all active products.
     */
    public function index(): void
    {
        try {
            $products = $this->productModel->all();

            $this->view('products/index', [
                'products' => $products,
                'title'    => 'Product List',
            ]);

        } catch (Throwable $e) {
            $this->logError('Index', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Show product creation form with available categories.
     */
    public function create(): void
    {
        try {
            $categories = $this->categoryModel->all();

            $this->view('products/create', [
                'categories' => $categories,
            ]);

        } catch (Throwable $e) {
            $this->logError('Create View', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Store a newly created product after validation.
     */
    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            $data = array_map(
                fn ($value) => is_scalar($value) ? trim((string) $value) : '',
                $_POST
            );

            $request = new ProductRequest($data);

            if (!$request->validate()) {
                $this->view('products/create', [
                    'errors'     => $request->getErrors(),
                    'categories' => $this->categoryModel->all(),
                    'old'        => $data,
                ]);
                return;
            }

            $productService = new ProductService();
            if ($productService->createProduct($data, $_FILES['image'] ?? [])) {
                $this->redirect('/products?success=created');
            } else {
                throw new \Exception("Could not create product.");
            }

        } catch (Throwable $e) {
            $this->logError('Store', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param string $id
     */
    public function edit(string $id): void
    {
        try {
            $product = $this->productModel->findById((int) $id);
            if (!$product) {
                $this->redirect('/products?error=not_found');
                return;
            }

            $this->view('products/edit', [
                'product'    => $product,
                'categories' => $this->categoryModel->all(),
                'title'      => 'Edit Product',
            ]);
        } catch (Throwable $e) {
            $this->logError('Edit View', $e);
            $this->view('errors/500');
        }
    }

    /**
     * Update the specified product in storage.
     * * @param string $id
     */
    /**
     * Update the specified product in storage.
     */
    public function update(string $id): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            // Sanitization via array_map to ensure all inputs are trimmed and safe for validation
            $data = array_map(
                fn ($value) => is_scalar($value) ? trim((string) $value) : '',
                $_POST
            );

            $request = new ProductRequest($data);

            // Validate in update mode (pass true)
            if (!$request->validate(true)) {
                $this->view('products/edit', [
                    'errors'     => $request->getErrors(),
                    'categories' => $this->categoryModel->all(),
                    // Pass current ID with the input data to retain state on error
                    'product'    => array_merge(['id' => $id], $data),
                ]);
                return;
            }

            // Prepare validated data array for the model
            $updateData = [
                'product_name'        => $data['product_name'],
                'sku'                 => $data['sku'],
                'description'         => $data['description'] ?? null,
                'category_id'         => (int) $data['category_id'],
                'price'               => (float) $data['price'],
                'stock_quantity'      => (int) $data['stock_quantity'],
                'low_stock_threshold' => (int) $data['low_stock_threshold'],
                'status'              => $data['status'] ?? 'active' // This will save 'inactive' properly
            ];

            // Use the new model method to update all fields
            if ($this->productModel->updateProduct((int)$id, $updateData)) {
                $this->redirect('/products?success=updated');
            } else {
                // Redirect on failure without full crash
                $this->redirect('/products');
            }

        } catch (Throwable $e) {
            $this->logError('Update Error', $e);
            $this->view('errors/500');
        }
    }

    /**
     * Soft delete a product by ID.
     *
     * @param string $id
     */
    public function destroy(string $id): void
    {
        try {
            $this->productModel->delete((int) $id);
            $this->redirect('/products?success=deleted');

        } catch (Throwable $e) {
            $this->logError('Destroy', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }
}