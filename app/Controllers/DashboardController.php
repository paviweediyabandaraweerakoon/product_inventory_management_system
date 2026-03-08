<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $productModel = new Product();
        $categoryModel = new Category();

        // 1. Live Category Distribution (Doughnut Chart එකට)
        // Stationery සහ Home Appliances වලට අදාළව නිෂ්පාදන ගණන ලබා ගනී
        $catSql = "SELECT c.category_name as name, COUNT(p.id) as count 
                   FROM categories c 
                   LEFT JOIN products p ON c.id = p.category_id 
                   WHERE c.status = 'active' AND c.deleted_at IS NULL 
                   GROUP BY c.id";

        $chartResults = $productModel->query($catSql)->fetchAll(\PDO::FETCH_ASSOC);
        $chartLabels = array_column($chartResults, 'name');
        $chartData   = array_column($chartResults, 'count');

        // 2. Live Sales Performance (Line Chart එකට - පසුගිය මාස 6ක දත්ත)
        // transaction_type = 'OUT' (විකුණුම්) වල එකතුව ගණනය කරයි
        $salesSql = "SELECT DATE_FORMAT(created_at, '%b') as month, SUM(quantity * unit_price) as total 
                     FROM inventory_transactions 
                     WHERE transaction_type = 'OUT' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                     GROUP BY MONTH(created_at) 
                     ORDER BY created_at ASC";

        $salesResults = $productModel->query($salesSql)->fetchAll(\PDO::FETCH_ASSOC);
        
        // දත්ත නැතිනම් Default මාස සහ 0 අගයන් පෙන්වීමට සකසයි
        $salesLabels = array_column($salesResults, 'month') ?: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $salesData   = array_column($salesResults, 'total') ?: [0, 0, 0, 0, 0, 0];

        // 3. Low Stock Calculation (Alert Card එකට)
        // තොගය 5ට වඩා අඩු නිෂ්පාදන ගණන බලයි
        $lowStockSql = "SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 5 AND deleted_at IS NULL";
        $lowStockResult = $productModel->query($lowStockSql)->fetch(\PDO::FETCH_ASSOC);

        // 4. Inventory Total Value (Total Value Card එකට)
        // මුළු ඉන්වෙන්ටරියේ වටිනාකම (Price * Stock) එකතු කරයි
        $valueSql = "SELECT SUM(price * stock_quantity) as total_value FROM products WHERE deleted_at IS NULL";
        $valueResult = $productModel->query($valueSql)->fetch(\PDO::FETCH_ASSOC);
        
        // දත්ත array එක සකස් කර View එකට යවයි
        $data = [
            'totalProducts'    => $productModel->countActiveRecords(), 
            'lowStockCount'    => (int)($lowStockResult['total'] ?? 0), 
            'inventoryValue'   => (float)($valueResult['total_value'] ?? 0),
            'activeCategories' => $categoryModel->countActiveRecords(), 
            'recentProducts'   => $productModel->getAll(5), // අලුත්ම නිෂ්පාදන 5ක් පෙන්වීමට
            'chartLabels'      => $chartLabels,
            'chartData'        => $chartData,
            'salesLabels'      => $salesLabels,
            'salesData'        => $salesData
        ];

        return $this->view('dashboard/index', $data);
    }
}