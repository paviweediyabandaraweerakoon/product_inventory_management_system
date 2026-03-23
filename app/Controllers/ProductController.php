<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FileUploadHelper;
use App\Models\Category;
use App\Models\Product;
use App\Requests\ProductRequest;
use Throwable;

/**
 * Class ProductController
 *
 * Responsibility:
 * Acts as a traffic cop between HTTP requests and the application's models.
 * Handles request coordination, validation flow, model interaction,
 * and final view/redirect responses for product operations.
 *
 * This controller should not contain direct SQL queries or heavy business rules.
 */
class ProductController extends Controller
{
    private const PER_PAGE = 10;

    private Product $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * Display paginated product list.
     */
    public function index(): void
    {
        try {
            $search = trim((string) ($_GET['search'] ?? ''));
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $limit = self::PER_PAGE;
            $offset = ($page - 1) * $limit;

            $products = $this->productModel->getPaginated($search, $limit, $offset);
            $totalProducts = $this->productModel->countFiltered($search);
            $totalPages = max(1, (int) ceil($totalProducts / $limit));

            $this->view('products/index', [
                'products'      => $products,
                'search'        => $search,
                'currentPage'   => $page,
                'perPage'       => $limit,
                'totalProducts' => $totalProducts,
                'totalPages'    => $totalPages,
                'title'         => 'Product Inventory',
            ]);
        } catch (Throwable $e) {
            $this->logError('ProductController@index', $e);
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Internal Server Error']);
        }
    }

    /**
     * Show create product form.
     */
    public function create(): void
    {
        try {
            $this->view('products/create', [
                'categories' => $this->categoryModel->all() ?: [],
                'old'        => [],
                'errors'     => [],
                'title'      => 'Add New Product',
            ]);
        } catch (Throwable $e) {
            $this->logError('ProductController@create', $e);
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Internal Server Error']);
        }
    }

    /**
     * Store new product.
     */
    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            if (!isset($_SESSION['user_id'])) {
                $this->redirect('/login');
                return;
            }

            $data = [];
            foreach ($_POST as $key => $value) {
                $data[$key] = trim((string) $value);
            }

            $request = new ProductRequest($data);

            if (!$request->validate()) {
                $this->view('products/create', [
                    'errors'     => $request->getErrors(),
                    'categories' => $this->categoryModel->all() ?: [],
                    'old'        => $data,
                    'title'      => 'Add New Product',
                ]);
                return;
            }

            $skuInput = trim((string) ($data['sku'] ?? ''));
            $sku = $skuInput !== ''
                ? strtoupper($skuInput)
                : 'SKU-' . strtoupper(bin2hex(random_bytes(4)));

            if ($this->productModel->skuExists($sku)) {
                $this->view('products/create', [
                    'errors'     => ['sku' => 'SKU already exists. Please use a unique SKU.'],
                    'categories' => $this->categoryModel->all() ?: [],
                    'old'        => $data,
                    'title'      => 'Add New Product',
                ]);
                return;
            }

            $uploadResult = FileUploadHelper::uploadProductImage($_FILES['image'] ?? []);

            if (!$uploadResult['success']) {
                $this->view('products/create', [
                    'errors'     => ['image' => $uploadResult['error'] ?? 'Image upload failed.'],
                    'categories' => $this->categoryModel->all() ?: [],
                    'old'        => $data,
                    'title'      => 'Add New Product',
                ]);
                return;
            }

            $this->productModel->create([
                'product_name'        => $data['product_name'],
                'sku'                 => $sku,
                'description'         => $data['description'] ?? null,
                'status'              => $data['status'] ?? 'active',
                'category_id'         => (int) $data['category_id'],
                'price'               => (float) $data['price'],
                'stock_quantity'      => (int) $data['stock_quantity'],
                'low_stock_threshold' => (int) $data['low_stock_threshold'],
                'image_path'          => $uploadResult['path'] ?? null,
                'created_by'          => (int) $_SESSION['user_id'],
            ]);

            $this->redirect('/products?success=created');
            return;
        } catch (Throwable $e) {
            $this->logError('ProductController@store', $e);
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Internal Server Error']);
        }
    }

    /**
     * Show product details.
     */
    public function show(string $id): void
    {
        try {
            $product = $this->productModel->findById((int) $id);

            if (!$product) {
                $this->redirect('/products?error=not_found');
                return;
            }

            $this->view('products/show', [
                'product' => $product,
                'title'   => 'Product Details',
            ]);
        } catch (Throwable $e) {
            $this->logError('ProductController@show', $e);
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Internal Server Error']);
        }
    }

    /**
     * Show edit form.
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
                'categories' => $this->categoryModel->all() ?: [],
                'errors'     => [],
                'title'      => 'Edit Product',
            ]);
        } catch (Throwable $e) {
            $this->logError('ProductController@edit', $e);
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Internal Server Error']);
        }
    }

    /**
     * Update product.
     */
    public function update(string $id): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            $productId = (int) $id;
            $existingProduct = $this->productModel->findById($productId);

            if (!$existingProduct) {
                $this->redirect('/products?error=not_found');
                return;
            }

            $data = [];
            foreach ($_POST as $key => $value) {
                $data[$key] = trim((string) $value);
            }

            $request = new ProductRequest($data);

            if (!$request->validate(true)) {
                $this->view('products/edit', [
                    'errors'     => $request->getErrors(),
                    'product'    => array_merge($existingProduct, $data),
                    'categories' => $this->categoryModel->all() ?: [],
                    'title'      => 'Edit Product',
                ]);
                return;
            }

            $skuInput = trim((string) ($data['sku'] ?? $existingProduct['sku']));
            $sku = strtoupper($skuInput);

            if ($this->productModel->skuExists($sku, $productId)) {
                $this->view('products/edit', [
                    'errors'     => ['sku' => 'SKU already exists. Please use a unique SKU.'],
                    'product'    => array_merge($existingProduct, $data),
                    'categories' => $this->categoryModel->all() ?: [],
                    'title'      => 'Edit Product',
                ]);
                return;
            }

            $imagePath = $existingProduct['image_path'] ?? null;
            $hasNewImage = isset($_FILES['image']) && !empty($_FILES['image']['name']);

            if ($hasNewImage) {
                $uploadResult = FileUploadHelper::uploadProductImage($_FILES['image']);

                if (!$uploadResult['success']) {
                    $this->view('products/edit', [
                        'errors'     => ['image' => $uploadResult['error'] ?? 'Image upload failed.'],
                        'product'    => array_merge($existingProduct, $data),
                        'categories' => $this->categoryModel->all() ?: [],
                        'title'      => 'Edit Product',
                    ]);
                    return;
                }

                if (!empty($uploadResult['path'])) {
                    FileUploadHelper::deleteProductImage($existingProduct['image_path'] ?? null);
                    $imagePath = $uploadResult['path'];
                }
            }

            $this->productModel->updateById($productId, [
                'product_name'        => $data['product_name'],
                'sku'                 => $sku,
                'description'         => $data['description'] ?? null,
                'status'              => $data['status'] ?? 'active',
                'category_id'         => (int) $data['category_id'],
                'price'               => (float) $data['price'],
                'stock_quantity'      => (int) $data['stock_quantity'],
                'low_stock_threshold' => (int) $data['low_stock_threshold'],
                'image_path'          => $imagePath,
            ]);

            $this->redirect('/products?success=updated');
            return;
        } catch (Throwable $e) {
            $this->logError('ProductController@update', $e);
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Internal Server Error']);
        }
    }

    /**
     * Soft delete product.
     */
    public function destroy(string $id): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/products');
                return;
            }

            $product = $this->productModel->findById((int) $id);

            if (!$product) {
                $this->redirect('/products?error=not_found');
                return;
            }

            $this->productModel->delete((int) $id);

            $this->redirect('/products?success=deleted');
            return;
        } catch (Throwable $e) {
            $this->logError('ProductController@destroy', $e);
            http_response_code(500);
            $this->view('errors/500', ['title' => 'Internal Server Error']);
        }
    }
}