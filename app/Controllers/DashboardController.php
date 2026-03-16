<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Core\Env;
use Throwable;

/**
 * Class DashboardController
 * Handles real-time inventory analytics, sales performance monitoring, 
 * and operational health tracking for the administration dashboard.
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
            // Configuration: Configurable via .env
            $reportMonths = (int) Env::get('DASHBOARD_REPORT_MONTHS', 6);
            $lowStockThreshold = (int) Env::get('LOW_STOCK_THRESHOLD', 5);

            // 1. Category Distribution: Active products count per active category
            $catSql = "SELECT c.category_name as name, COUNT(p.id) as count 
                       FROM categories c 
                       LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
                       WHERE c.status = 'active' AND c.deleted_at IS NULL 
                       GROUP BY c.id";

            $chartResults = $this->categoryModel->query($catSql)->fetchAll(\PDO::FETCH_ASSOC);
            $chartLabels = array_column($chartResults, 'name');
            $chartData   = array_column($chartResults, 'count');

            // 2. Sales Performance: Monthly totals with COALESCE for zero-handling
            $salesSql = "SELECT 
                            DATE_FORMAT(created_at, '%b %Y') as month_year, 
                            COALESCE(SUM(quantity * unit_price), 0) as total 
                         FROM inventory_transactions 
                         WHERE transaction_type = 'OUT' 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                         GROUP BY YEAR(created_at), MONTH(created_at)
                         ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC";

            $salesResults = $this->transactionModel->query($salesSql, [$reportMonths])->fetchAll(\PDO::FETCH_ASSOC);
            $salesLabels = array_column($salesResults, 'month_year');
            $salesData   = array_column($salesResults, 'total');

            // 3. Low Stock: Threshold monitored from .env
            $lowStockSql = "SELECT COUNT(*) as total FROM products WHERE stock_quantity <= ? AND deleted_at IS NULL";
            $lowStockResult = $this->productModel->query($lowStockSql, [$lowStockThreshold])->fetch(\PDO::FETCH_ASSOC);
            $lowStockCount = $lowStockResult['total'] ?? 0;

            // 4. Inventory Value: Real-time stock valuation
            $valueSql = "SELECT COALESCE(SUM(price * stock_quantity), 0) as total_value FROM products WHERE deleted_at IS NULL";
            $valueResult = $this->productModel->query($valueSql)->fetch(\PDO::FETCH_ASSOC);
            $totalValue = $valueResult['total_value'] ?? 0;

            // 5. Recent Activity: Latest stock movements (Filtered for active products)
            $activitySql = "SELECT t.*, p.product_name 
                            FROM inventory_transactions t
                            JOIN products p ON t.product_id = p.id
                            WHERE p.deleted_at IS NULL
                            ORDER BY t.created_at DESC 
                            LIMIT 10";
            $recentActivity = $this->transactionModel->query($activitySql)->fetchAll(\PDO::FETCH_ASSOC);

            // 6. Recent Products: Latest 5 additions (Added id DESC for secondary sort)
            $recentProductsSql = "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY created_at DESC, id DESC LIMIT 5";
            $recentProducts = $this->productModel->query($recentProductsSql)->fetchAll(\PDO::FETCH_ASSOC);

            $data = [
                'totalProducts'    => $this->productModel->countActiveRecords(), 
                'lowStockCount'    => $lowStockCount, 
                'inventoryValue'   => number_format($totalValue, 2),
                'activeCategories' => $this->categoryModel->countActiveRecords(), 
                'recentProducts'   => $recentProducts, 
                'recentActivity'   => $recentActivity, 
                'chartLabels'      => $chartLabels,
                'chartData'        => $chartData,
                'salesLabels'      => $salesLabels,
                'salesData'        => $salesData
            ];

            $this->view('dashboard/index', $data);

        } catch (Throwable $e) {
            error_log(sprintf(
                "[%s] Dashboard Index Error: %s in %s on line %d",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
            http_response_code(500);
            $this->view('errors/500');
            exit; 
        }
    }
}