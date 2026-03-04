<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\InventoryTransaction;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index()
    {
        $productModel = new Product();
        $transactionModel = new InventoryTransaction();

        // fetch data for dashboard
        $data = [
            'totalProducts' => $productModel->getCount(),
            'lowStockCount' => 0,
            'recentProducts' => $productModel->getAll(5, 0),
            'recentActivity' => []
        ];

        // load view(app/Views/dashboard/index.php)
        return $this->view('dashboard/index', $data);
    }
}