<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Requests\ProductRequest; // use the ProductRequest class for validation

class ProductController extends Controller
{
    // 1. show all products
    public function index()
    {
        $productModel = new Product();
        $products = $productModel->all(); 

        return $this->view('products/index', [
            'products' => $products,
            'title' => 'Product List'
        ]);
    }

    // 2. show form for adding new product
    public function create()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->all(); 

        return $this->view('products/create', [
            'categories' => $categories
        ]);
    }

    // 3. Save data from create form to database
    public function store()
    {
        // Validation
        $request = new ProductRequest($_POST);

        if (!$request->validate()) {
            // if have validation errors, go back to form with errors and old input
            $categoryModel = new Category();
            return $this->view('products/create', [
                'errors' => $request->getErrors(),
                'categories' => $categoryModel->all(),
                'old' => $_POST // Old data, go back to form with old input
            ]);
        }

        // if validation passed, save the product
        $productModel = new Product();
        $productModel->create([
            'product_name'   => $_POST['product_name'],
            'category_id'    => $_POST['category_id'],
            'price'          => $_POST['price'],
            'stock_quantity' => $_POST['stock_quantity'],
            'status'         => 'active'
        ]);

        // If everything is fine, redirect to product list
        header('Location: /products');
        exit;
    }
}