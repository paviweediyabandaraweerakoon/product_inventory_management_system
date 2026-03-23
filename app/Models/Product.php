<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class Product
 *
 * Responsibility:
 * Handle database operations for the products table only.
 */
class Product extends Model
{
    /**
     * Table name.
     */
    protected string $table = 'products';

    /**
     * Get paginated non-deleted products with category info and optional search.
     *
     * @param string $search Search keyword
     * @param int    $limit  Records per page
     * @param int    $offset Offset
     *
     * @return array<int,array<string,mixed>>
     */
    public function getPaginated(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT
                    p.id,
                    p.product_name,
                    p.sku,
                    p.description,
                    p.status,
                    p.category_id,
                    p.price,
                    p.stock_quantity,
                    p.low_stock_threshold,
                    p.image_path,
                    p.created_by,
                    p.created_at,
                    p.updated_at,
                    c.category_name
                FROM {$this->table} p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                   AND c.deleted_at IS NULL
                WHERE p.deleted_at IS NULL";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (p.product_name LIKE :search OR p.sku LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY p.created_at DESC, p.id DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count non-deleted products with optional search.
     *
     * @param string $search Search keyword
     *
     * @return int
     */
    public function countFiltered(string $search = ''): int
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->table}
                WHERE deleted_at IS NULL";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (product_name LIKE :search OR sku LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Get all non-deleted products with category info.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->getPaginated('', 100000, 0);
    }

    /**
     * Find product by ID with category name.
     *
     * @param int $id Product ID
     *
     * @return array<string,mixed>|false
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT
                    p.id,
                    p.product_name,
                    p.sku,
                    p.description,
                    p.status,
                    p.category_id,
                    p.price,
                    p.stock_quantity,
                    p.low_stock_threshold,
                    p.image_path,
                    p.created_by,
                    p.created_at,
                    p.updated_at,
                    c.category_name
                FROM {$this->table} p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                   AND c.deleted_at IS NULL
                WHERE p.id = :id
                  AND p.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new product.
     *
     * @param array<string,mixed> $data Insert data
     *
     * @return string|false
     */
    public function create(array $data): string|false
    {
        $sql = "INSERT INTO {$this->table}
                    (
                        product_name,
                        sku,
                        description,
                        status,
                        category_id,
                        price,
                        stock_quantity,
                        low_stock_threshold,
                        image_path,
                        created_by,
                        created_at
                    )
                VALUES
                    (
                        :product_name,
                        :sku,
                        :description,
                        :status,
                        :category_id,
                        :price,
                        :stock_quantity,
                        :low_stock_threshold,
                        :image_path,
                        :created_by,
                        NOW()
                    )";

        $this->query($sql, $data);

        return $this->db->lastInsertId();
    }

    /**
     * Update existing product by ID.
     *
     * @param int                 $id   Product ID
     * @param array<string,mixed> $data Update data
     *
     * @return bool
     */
    public function updateById(int $id, array $data): bool
    {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }

        $params['id'] = $id;

        $sql = "UPDATE {$this->table}
                SET " . implode(', ', $fields) . ", updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        return $this->query($sql, $params)->rowCount() > 0;
    }

    /**
     * Soft delete product by ID.
     *
     * @param int $id Product ID
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE {$this->table}
                SET deleted_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Check whether a SKU already exists.
     *
     * @param string   $sku       SKU value
     * @param int|null $excludeId Exclude current product ID for update
     *
     * @return bool
     */
    public function skuExists(string $sku, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->table}
                WHERE sku = :sku
                  AND deleted_at IS NULL";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sku', $sku, PDO::PARAM_STR);

        if ($excludeId !== null) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Count active (non-deleted) products.
     *
     * @return int
     */
    public function countActiveRecords(): int
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->table}
                WHERE deleted_at IS NULL
                  AND status = 'active'";

        return (int) $this->query($sql)->fetchColumn();
    }

    /**
     * Count low stock active products.
     *
     * @param int $threshold Threshold
     *
     * @return int
     */
    public function countLowStockProducts(int $threshold): int
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->table}
                WHERE stock_quantity <= :threshold
                  AND status = 'active'
                  AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Get total inventory value for active products.
     *
     * @return float
     */
    public function getTotalInventoryValue(): float
    {
        $sql = "SELECT COALESCE(SUM(price * stock_quantity), 0) AS total_value
                FROM {$this->table}
                WHERE status = 'active'
                  AND deleted_at IS NULL";

        $result = $this->query($sql)->fetch(PDO::FETCH_ASSOC);

        return (float) ($result['total_value'] ?? 0);
    }

    /**
     * Get product distribution per active category.
     *
     * @return array<int,array<string,mixed>>
     */
    public function getCategoryDistribution(): array
    {
        $sql = "SELECT
                    c.category_name AS name,
                    COUNT(p.id) AS count
                FROM categories c
                LEFT JOIN {$this->table} p
                    ON c.id = p.category_id
                   AND p.deleted_at IS NULL
                   AND p.status = 'active'
                WHERE c.status = 'active'
                  AND c.deleted_at IS NULL
                GROUP BY c.id, c.category_name
                ORDER BY c.category_name ASC";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get latest active products.
     *
     * @param int $limit Number of records
     *
     * @return array<int,array<string,mixed>>
     */
    public function getRecentProducts(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        $sql = "SELECT
                    id,
                    product_name,
                    sku,
                    stock_quantity,
                    price,
                    created_at
                FROM {$this->table}
                WHERE deleted_at IS NULL
                  AND status = 'active'
                ORDER BY created_at DESC, id DESC
                LIMIT {$limit}";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}