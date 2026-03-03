<?php

namespace App\Controllers;

use App\Core\Controller;

class CategoryController extends Controller
{
    /**
     * Show list of categories
     */
    public function index()
    {
        $categories = \App\Models\Category::all();
        // In a real app you would pass data to the view, this is a stub
        require __DIR__ . '/../Views/categories/index.php';
    }

    /**
     * Display form and handle submission for creating a category
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? null;
            if ($name) {
                \App\Models\Category::create(['name' => $name]);
                header('Location: /categories');
                exit;
            }
        }

        require __DIR__ . '/../Views/categories/create.php';
    }
}
