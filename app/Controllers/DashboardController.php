<?php

namespace App\Controllers;

use App\Core\Controller;
// Temporary comments for models that we haven't created yet, to avoid errors in this branch
// use App\Models\Product;
// use App\Models\InventoryTransaction;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index()
    {
        // Temporary comments for models that we haven't created yet, to avoid errors in this branch
        // $productModel = new Product();
        // $transactionModel = new InventoryTransaction();

        // 2.Temporary data for dashboard metrics, replace with actual queries once models are implemented
        // so, we can avoid errors in this branch and continue with the development of other features
        $data = [
            'totalProducts' => 0, 
            'lowStockCount' => 0,
            'recentProducts' => [], 
            'recentActivity' => []
        ];

        // 3. Load the dashboard view with the data
        return $this->view('dashboard/index', $data);
    }
}