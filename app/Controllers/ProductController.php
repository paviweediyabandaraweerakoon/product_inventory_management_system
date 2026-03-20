<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Requests\ProductRequest;
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

            // Trim all scalar inputs
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

            $this->productModel->create([
                'product_name'   => $data['product_name'],
                'category_id'    => (int) $data['category_id'],
                'price'          => (float) $data['price'],
                'stock_quantity' => (int) $data['stock_quantity'],
                'status'         => 'active',
            ]);

            $this->redirect('/products?success=created');

        } catch (Throwable $e) {
            $this->logError('Store', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
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