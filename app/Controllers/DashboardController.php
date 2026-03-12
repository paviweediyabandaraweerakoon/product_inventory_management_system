<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;
use Throwable;

/**
 * Class DashboardController
 * * Responsible for gathering and processing analytics, stock levels, 
 * and sales performance data to be displayed on the main admin dashboard.
 */
class DashboardController extends Controller
{
    /** @var Product */
    private Product $productModel;

    /** @var Category */
    private Category $categoryModel;

    /** @var InventoryTransaction */
    private InventoryTransaction $transactionModel;

    /**
     * DashboardController constructor.
     * Initializes the required models as controller properties.
     */
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->transactionModel = new InventoryTransaction();
    }

    /**
     * Display the dashboard with live statistics and charts.
     * * @return void
     */
    public function index(): void
    {
        try {
            // Configurable report period (e.g., last 6 months)
            $reportMonths = 6;

            // 1. Live Category Distribution (Considering Soft Deletes)
            $catSql = "SELECT c.category_name as name, COUNT(p.id) as count 
                       FROM categories c 
                       LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
                       WHERE c.status = 'active' AND c.deleted_at IS NULL 
                       GROUP BY c.id";

            $chartResults = $this->categoryModel->query($catSql)->fetchAll(\PDO::FETCH_ASSOC);
            $chartLabels = array_column($chartResults, 'name');
            $chartData   = array_column($chartResults, 'count');

            // 2. Live Sales Performance (Dynamic Month Generation)
            $salesSql = "SELECT 
                            DATE_FORMAT(created_at, '%b') as month, 
                            SUM(quantity * unit_price) as total 
                         FROM inventory_transactions 
                         WHERE transaction_type = 'OUT' 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                         GROUP BY YEAR(created_at), MONTH(created_at)
                         ORDER BY created_at ASC";

            $salesResults = $this->transactionModel->query($salesSql, [$reportMonths])->fetchAll(\PDO::FETCH_ASSOC);
            $salesLabels = array_column($salesResults, 'month');
            $salesData   = array_column($salesResults, 'total');

            // Fallback for empty sales data
            if (empty($salesLabels)) {
                $salesLabels = ['No Data'];
                $salesData   = [0];
            }

            // 3. Low Stock Calculation
            $lowStockSql = "SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 5 AND deleted_at IS NULL";
            $lowStockResult = $this->productModel->query($lowStockSql)->fetch(\PDO::FETCH_ASSOC);
            $lowStockCount = $lowStockResult['total'] ?? 0;

            // 4. Inventory Total Value
            $valueSql = "SELECT SUM(price * stock_quantity) as total_value FROM products WHERE deleted_at IS NULL";
            $valueResult = $this->productModel->query($valueSql)->fetch(\PDO::FETCH_ASSOC);
            $totalValue = $valueResult['total_value'] ?? 0;

            // 5. Recent Activity (LIVE)
            // Fetching last 10 transactions to show real-time movement
            $activitySql = "SELECT t.*, p.product_name 
                            FROM inventory_transactions t
                            JOIN products p ON t.product_id = p.id
                            ORDER BY t.created_at DESC LIMIT 10";
            $recentActivity = $this->transactionModel->query($activitySql)->fetchAll(\PDO::FETCH_ASSOC);

            // Prepare data array for the view
            $data = [
                'totalProducts'    => $this->productModel->countActiveRecords(), 
                'lowStockCount'    => $lowStockCount, 
                'inventoryValue'   => number_format($totalValue, 2),
                'activeCategories' => $this->categoryModel->countActiveRecords(), 
                'recentProducts'   => $this->productModel->all(), // Assuming all() handles soft deletes
                'recentActivity'   => $recentActivity, 
                'chartLabels'      => $chartLabels,
                'chartData'        => $chartData,
                'salesLabels'      => $salesLabels,
                'salesData'        => $salesData
            ];

            $this->view('dashboard/index', $data);

        } catch (Throwable $e) {
            // Systematic error logging with context
            error_log(sprintf(
                "[%s] Dashboard Error: %s in %s on line %d",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
            
            $this->view('errors/500'); 
        }
    }
}