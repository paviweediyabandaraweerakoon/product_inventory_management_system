<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\InventoryTransaction;

class InventoryController extends Controller {

    // show stock adjustment form 
    public function adjust($productId) {
        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product) {
            header('Location: /products?error=not_found');
            exit;
        }

        return $this->view('inventory/adjust', ['product' => $product]);
    }

    // Save stock adjustment
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $type = $_POST['transaction_type']; // IN or OUT
            $qty = (int)$_POST['quantity'];
            $reason = $_POST['reason'];
            $userId = $_SESSION['user_id'] ?? 1; // get from session in real world, this is temporary

            $productModel = new Product();
            $transactionModel = new InventoryTransaction();

            $product = $productModel->find($productId);
            
            // new stock calculate
            $currentStock = (int)$product['stock_quantity'];
            $newStock = ($type === 'IN') ? ($currentStock + $qty) : ($currentStock - $qty);

            if ($newStock < 0) {
                header("Location: /inventory/adjust/$productId?error=insufficient_stock");
                exit;
            }

            // 1. In Product Table, update the stock quantity 
            $productModel->updateStock($productId, $newStock);

            // 2. Transaction Log
            $transactionModel->log($productId, $type, $qty, $reason, $userId);

            header("Location: /products?success=stock_updated");
            exit;
        }
    }
}