<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $productModel = new Product();
        
    
        $products = $productModel->getAll();

    
        return $this->view('products/index', [
            'products' => $products,
            'title' => 'Product List'
        ]);
    }
}