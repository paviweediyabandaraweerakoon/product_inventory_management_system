<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;
use Throwable;

/**
 * Class DashboardController
 * Handles the main dashboard logic with live statistics and charts.
 */
class DashboardController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    private InventoryTransaction $transactionModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->transactionModel = new InventoryTransaction();
    }

    public function index(): void
    {
        try {
            // 1. Live Category Distribution
            $catSql = "SELECT c.category_name as name, COUNT(p.id) as count 
                       FROM categories c 
                       LEFT JOIN products p ON c.id = p.category_id 
                       WHERE c.status = 'active' AND c.deleted_at IS NULL 
                       GROUP BY c.id";

            $chartResults = $this->categoryModel->query($catSql)->fetchAll(\PDO::FETCH_ASSOC);
            $chartLabels = array_column($chartResults, 'name');
            $chartData   = array_column($chartResults, 'count');

            // 2. Live Sales Performance (Last 6 Months)
            $salesSql = "SELECT 
                            DATE_FORMAT(created_at, '%b') as month, 
                            SUM(quantity * unit_price) as total 
                         FROM inventory_transactions 
                         WHERE transaction_type = 'OUT' 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                         GROUP BY MONTH(created_at)
                         ORDER BY created_at ASC";

            $salesResults = $this->transactionModel->query($salesSql)->fetchAll(\PDO::FETCH_ASSOC);
            $salesLabels = array_column($salesResults, 'month');
            $salesData   = array_column($salesResults, 'total');

            if (empty($salesLabels)) {
                $salesLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                $salesData   = [0, 0, 0, 0, 0, 0];
            }

            // 3. Low Stock Calculation
            $lowStockSql = "SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 5 AND deleted_at IS NULL";
            $lowStockResult = $this->productModel->query($lowStockSql)->fetch(\PDO::FETCH_ASSOC);
            $lowStockCount = $lowStockResult['total'] ?? 0;

            // 4. Inventory Total Value
            $valueSql = "SELECT SUM(price * stock_quantity) as total_value FROM products WHERE deleted_at IS NULL";
            $valueResult = $this->productModel->query($valueSql)->fetch(\PDO::FETCH_ASSOC);
            $totalValue = $valueResult['total_value'] ?? 0;

            // Prepare data array for the view
            //collect all the data we need for the dashboard widgets and charts into a single array to pass to the view
            $data = [
                'totalProducts'    => $this->productModel->countActiveRecords(), 
                'lowStockCount'    => $lowStockCount, 
                'inventoryValue'   => number_format($totalValue, 2),
                'activeCategories' => $this->categoryModel->countActiveRecords(), 
                'recentProducts'   => $this->productModel->all(), 
                'recentActivity'   => [], 
                'chartLabels'      => $chartLabels,
                'chartData'        => $chartData,
                'salesLabels'      => $salesLabels,
                'salesData'        => $salesData
            ];

            $this->view('dashboard/index', $data);

        } catch (Throwable $e) {
            error_log("Dashboard Error: " . $e->getMessage());
            
            // die($e->getMessage()); 
            $this->view('errors/500'); 
        }
    }
}