<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with 100% Live data
     */
    public function index()
    {
        $productModel = new Product();
        $categoryModel = new Category();
        $transactionModel = new InventoryTransaction();

        // 1. Live Category Distribution: Count of products in each active category
        $catSql = "SELECT c.category_name as name, COUNT(p.id) as count 
                   FROM categories c 
                   LEFT JOIN products p ON c.id = p.category_id 
                   WHERE c.status = 'active' AND c.deleted_at IS NULL 
                   GROUP BY c.id";

        $chartResults = $productModel->query($catSql)->fetchAll(\PDO::FETCH_ASSOC);
        $chartLabels = array_column($chartResults, 'name');
        $chartData   = array_column($chartResults, 'count');

        // 2. Live Sales Performance: last 6 months sales data
        
        $salesSql = "SELECT 
                        DATE_FORMAT(created_at, '%b') as month, 
                        SUM(quantity * unit_price) as total 
                     FROM inventory_transactions 
                     WHERE transaction_type = 'OUT' 
                     AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                     GROUP BY MONTH(created_at)
                     ORDER BY created_at ASC";

        $salesResults = $productModel->query($salesSql)->fetchAll(\PDO::FETCH_ASSOC);
        
        $salesLabels = array_column($salesResults, 'month');
        $salesData   = array_column($salesResults, 'total');

        // If no sales data, initialize with empty values for chart
        if (empty($salesLabels)) {
            $salesLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $salesData   = [0, 0, 0, 0, 0, 0];
        }

        // 3. Low Stock Calculation: less than or equal to 5 units in stock
        $lowStockSql = "SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 5 AND deleted_at IS NULL";
        $lowStockResult = $productModel->query($lowStockSql)->fetch(\PDO::FETCH_ASSOC);
        $lowStockCount = $lowStockResult['total'] ?? 0;

        // 4. Inventory Total Value: Full value of inventory
        $valueSql = "SELECT SUM(price * stock_quantity) as total_value FROM products WHERE deleted_at IS NULL";
        $valueResult = $productModel->query($valueSql)->fetch(\PDO::FETCH_ASSOC);
        $totalValue = $valueResult['total_value'] ?? 0;

        // send all data to view
        $data = [
            'totalProducts'    => $productModel->countActiveRecords(), 
            'lowStockCount'    => $lowStockCount, 
            'inventoryValue'   => number_format($totalValue, 2),
            'activeCategories' => $categoryModel->countActiveRecords(), 
            'recentProducts'   => $productModel->all(), 
            'recentActivity'   => [], 
            'chartLabels'      => $chartLabels,
            'chartData'        => $chartData,
            'salesLabels'      => $salesLabels,
            'salesData'        => $salesData
        ];

        return $this->view('dashboard/index', $data);
    }
}