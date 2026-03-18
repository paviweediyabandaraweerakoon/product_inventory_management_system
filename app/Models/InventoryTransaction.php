<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class InventoryTransaction
 * Manages stock movement logs (IN/OUT) and dashboard-level reporting queries.
 */
class InventoryTransaction extends Model
{
    protected string $table = 'inventory_transactions';

    /**
     * Log a new stock transaction using standard model handlers.
     */
    public function log(
        int $productId,
        string $type,
        int $quantity,
        string $note = '',
        float $unitPrice = 0.0,
        ?int $userId = null
    ): string|false {
        $timestamp = date('Y-m-d H:i:s');

        $data = [
            'product_id'       => $productId,
            'transaction_type' => strtoupper($type),
            'quantity'         => $quantity,
            'unit_price'       => $unitPrice,
            'reason'           => $note,
            'transaction_date' => $timestamp,
            'user_id'          => $userId ?? ($_SESSION['user_id'] ?? 1),
            'created_at'       => $timestamp,
        ];

        return $this->create($data);
    }

    /**
     * Create a new inventory transaction record.
     */
    public function create(array $data): string|false
    {
        $sql = "INSERT INTO {$this->table}
                    (product_id, transaction_type, quantity, unit_price, reason, transaction_date, user_id, created_at)
                VALUES
                    (:product_id, :transaction_type, :quantity, :unit_price, :reason, :transaction_date, :user_id, :created_at)";

        $this->query($sql, $data);

        return $this->db->lastInsertId();
    }

    /**
     * Get monthly sales totals for the last N months.
     * Returns only months that have data; the controller fills missing months with zero.
     */
    public function getMonthlySalesTotals(int $months): array
    {
        $months = max(1, (int) $months);

        $sql = "SELECT
                    DATE_FORMAT(transaction_date, '%Y-%m') AS month_key,
                    COALESCE(SUM(quantity * unit_price), 0) AS total
                FROM {$this->table}
                WHERE transaction_type = 'OUT'
                  AND transaction_date >= DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-01'), INTERVAL ? MONTH)
                GROUP BY YEAR(transaction_date), MONTH(transaction_date)
                ORDER BY YEAR(transaction_date) ASC, MONTH(transaction_date) ASC";

        return $this->query($sql, [$months - 1])->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent live stock movements with product details.
     */
    public function getRecentActivity(int $limit = 10): array
    {
        $limit = max(1, (int) $limit);

        $sql = "SELECT t.*, p.product_name
                FROM {$this->table} t
                INNER JOIN products p ON t.product_id = p.id
                WHERE p.deleted_at IS NULL
                ORDER BY t.transaction_date DESC, t.id DESC
                LIMIT {$limit}";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}