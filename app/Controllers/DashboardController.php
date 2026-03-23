<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Env;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Throwable;

/**
 * Class DashboardController
 * Handles real-time inventory analytics, sales performance monitoring,
 * and operational health insights for the administration dashboard.
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

    /**
     * Display the main dashboard with live metrics and recent operational data.
     */
    public function index(): void
    {
        try {
            // Configurable reporting values from .env
            $reportMonths = max(1, (int) Env::get('DASHBOARD_REPORT_MONTHS', 6));
            $lowStockThreshold = (int) Env::get('LOW_STOCK_THRESHOLD', 5);

            // 1. Category distribution (active products per active category)
            $chartResults = $this->productModel->getCategoryDistribution();
            $chartLabels = array_column($chartResults, 'name');
            $chartData   = array_map('intval', array_column($chartResults, 'count'));

            // 2. Sales performance (current month backwards, zero-filled)
            $salesResults = $this->transactionModel->getMonthlySalesTotals($reportMonths);
            [$salesLabels, $salesData] = $this->buildSalesSeries($salesResults, $reportMonths);

            // 3. Summary metrics
            $lowStockCount = $this->productModel->countLowStockProducts($lowStockThreshold);
            $totalValue = $this->productModel->getTotalInventoryValue();

            // 4. Recent live activity + recent products
            $recentActivity = $this->transactionModel->getRecentActivity(10);
            $recentProducts = $this->productModel->getRecentProducts(5);

            $data = [
                'totalProducts'      => $this->productModel->countActiveRecords(),
                'lowStockCount'      => $lowStockCount,
                'inventoryValue'     => number_format($totalValue, 2),
                'activeCategories'   => $this->categoryModel->countActiveRecords(),
                'recentProducts'     => $recentProducts,
                'recentActivity'     => $recentActivity,
                'chartLabels'        => $chartLabels,
                'chartData'          => $chartData,
                'salesLabels'        => $salesLabels,
                'salesData'          => $salesData,
                'reportMonths'       => $reportMonths,
                'lowStockThreshold'  => $lowStockThreshold,
            ];

            $this->view('dashboard/index', $data);

        } catch (Throwable $e) {
            $this->logError('Index', $e);
            http_response_code(500);
            $this->view('errors/500');
            exit;
        }
    }

    /**
     * Build month labels from the current month backwards and fill missing months with zero.
     *
     * @param array $salesResults
     * @param int   $reportMonths
     * @return array{0: array, 1: array}
     */
    private function buildSalesSeries(array $salesResults, int $reportMonths): array
    {
        $salesMap = [];

        foreach ($salesResults as $row) {
            $monthKey = $row['month_key'] ?? null;

            if ($monthKey !== null) {
                $salesMap[$monthKey] = (float) ($row['total'] ?? 0);
            }
        }

        $labels = [];
        $data = [];

        // Oldest -> current month
        for ($i = $reportMonths - 1; $i >= 0; $i--) {
            $date = new \DateTime('first day of this month');
            $date->modify("-{$i} month");

            $monthKey = $date->format('Y-m');
            $labels[] = $date->format('M Y');
            $data[] = $salesMap[$monthKey] ?? 0.0;
        }

        return [$labels, $data];
    }
}