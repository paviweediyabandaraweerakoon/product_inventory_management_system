<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Requests\ProductRequest;
use App\Services\ProductService;
use App\Services\ProductImportService;
use Throwable;
use Exception;

/**
 * Class ProductController
 * Manages product lifecycle operations including listing, creation,
 * validation, and soft-delete handling.
 */
class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    private ProductService $productService;
    private ProductImportService $productImportService;

    public function __construct()
    {
        parent::__construct();
        
        // Dynamic Global Session Lifecycle Lock (Centralized Management)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->productService = new ProductService();
        $this->productImportService = new ProductImportService();
    }

    /**
     * Display all active products.
     */
    public function index(): void
    {
        try {
            $importTelemetry = null;
            if (isset($_GET['import_status'], $_SESSION['import_telemetry']) && $_GET['import_status'] === 'success') {
                $importTelemetry = $_SESSION['import_telemetry'];
                unset($_SESSION['import_telemetry']);
            }

            $products = $this->productModel->all();

            $this->view('products/index', [
                'products'        => $products,
                'title'           => 'Product List',
                'importTelemetry' => $importTelemetry,
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

            $data = $this->getPostData();
            $request = new ProductRequest($data);

            if (!$request->validate()) {
                $this->view('products/create', [
                    'errors'     => $request->getErrors(),
                    'categories' => $this->categoryModel->all(),
                    'old'        => $data,
                ]);
                return;
            }

            if ($this->productService->createProduct($data, $_FILES['image'] ?? [])) {
                $this->redirect('/products?success=created');
            } else {
                throw new Exception("Could not create product.");
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
     * @param int $id
     */
    public function edit(int $id): void
    {
        try {
            $product = $this->productModel->find($id);
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
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Update the specified product in storage.
     * * @param int $id
     */
    public function update(int $id): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            $data = $this->getPostData();
            $request = new ProductRequest($data);

            if (!$request->validate(true)) {
                $this->view('products/edit', [
                    'errors'     => $request->getErrors(),
                    'categories' => $this->categoryModel->all(),
                    'product'    => array_merge(['id' => $id], $data),
                ]);
                return;
            }

            if ($this->productService->updateProduct($id, $data)) {
                $this->redirect('/products?success=updated');
            } else {
                error_log('Product update failed for ID: ' . $id);
                $this->redirect('/products?error=update_failed');
            }

        } catch (Throwable $e) {
            $this->logError('Update Error', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Soft delete a product by ID.
     *
     * @param int $id
     */
    public function destroy(int $id): void
    {
        try {
            $product = $this->productModel->find($id);
            if (!$product) {
                $this->redirect('/products?error=not_found');
                return;
            }

            $this->productModel->delete($id);
            $this->redirect('/products?success=deleted');

        } catch (Throwable $e) {
            $this->logError('Destroy', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Bulk Ingestion Framework with Schema Lock & Robust Boundaries
     */
    public function import(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            $fileData = $_FILES['csv_file'] ?? [];
            if (empty($fileData) || $fileData['error'] !== UPLOAD_ERR_OK) {
                $this->redirect('/products?error=upload_failed');
                return;
            }

            $currentUserId = $_SESSION['user_id'] ?? 1;

            // Process the CSV and ingest products, capturing metrics for telemetry
            $metrics = $this->productImportService->importProductsFromCsv($fileData, $currentUserId);

            $_SESSION['import_telemetry'] = [
                'processed' => $metrics['success'],
                'skipped'   => $metrics['skipped']
            ];

            $this->redirect("/products?import_status=success");

        } catch (Throwable $e) {
            error_log('Bulk Import Ingestion Fault: ' . $e->getMessage());
            $this->redirect('/products?error=ingestion_runtime_fault');
        }
    }
}