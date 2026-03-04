<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\InventoryTransaction;
use App\Requests\InventoryRequest; 

class InventoryController extends Controller {

    public function adjust($productId) {
        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product) {
            header('Location: /products?error=not_found');
            exit;
        }
        return $this->view('inventory/adjust', ['product' => $product]);
    }

    public function update() {
        // Validation
        $request = new InventoryRequest();
        $errors = $request->validate($_POST);

        if (!empty($errors)) {
            header("Location: /inventory/adjust/" . $_POST['product_id'] . "?error=" . urlencode($errors[0]));
            exit;
        }

        $productId = $_POST['product_id'];
        $type = $_POST['transaction_type'];
        $qty = (int)$_POST['quantity'];
        $reason = $_POST['reason'];
        $userId = $_SESSION['user_id'] ?? 1;

        $productModel = new Product();
        $transactionModel = new InventoryTransaction();

        
        try {
            
            $productModel->updateStockWithTransaction($productId, $type, $qty, $reason, $userId);
            header("Location: /products?success=stock_updated");
        } catch (\Exception $e) {
            header("Location: /inventory/adjust/$productId?error=" . urlencode($e->getMessage()));
        }
        exit;
    }
}