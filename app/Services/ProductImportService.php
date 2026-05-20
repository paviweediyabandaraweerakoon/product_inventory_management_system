<?php

namespace App\Services;

use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Throwable;

/**
 * Class ProductImportService
 * Handles isolated CSV processing and bulk data ingestion flows.
 */
class ProductImportService
{
    private const CSV_MAX_UPLOAD_SIZE = 5 * 1024 * 1024;
    private Product $productModel;
    private ProductService $productService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->productService = new ProductService();
    }

    /**
     * Enterprise Bulk Ingestion Pipeline with Schema Lock & Robust Boundaries
     */
    public function importProductsFromCsv(array $file, int $userId): array
    {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Invalid or corrupted CSV file stream uploaded.");
        }

        if (($file['size'] ?? 0) > self::CSV_MAX_UPLOAD_SIZE) {
            throw new Exception("CSV upload exceeded the maximum allowed size of 5MB.");
        }

        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv') {
            throw new Exception("Invalid file extension. Ingestion pipeline only accepts .csv resource formats.");
        }

        if (($handle = fopen($file['tmp_name'], 'r')) === false) {
            throw new Exception("Failed to open uploaded CSV resource stream.");
        }

        // Schema Lock Validation
        $headers = fgetcsv($handle, 1000, ',');
        $expectedSchema = ['product_name', 'sku', 'description', 'category_id', 'price', 'stock_quantity', 'low_stock_threshold'];
        $sanitizedHeaders = array_map('strtolower', array_map('trim', $headers ?: []));
        
        if ($sanitizedHeaders !== $expectedSchema) {
            fclose($handle);
            error_log(json_encode([
                'event' => 'csv_ingestion_schema_mismatch',
                'level' => 'CRITICAL'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            throw new Exception("CSV Schema lock broken. Column order or naming mismatch detected.");
        }

        $metrics = [
            'success' => 0,
            'skipped' => 0,
            'failed_samples' => []
        ];
        
        $lineNum = 1;

        // Abstracted Unit of Work Initiation via Model Transaction Boundary
        $this->productModel->beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $lineNum++;

                if (count(array_filter($row, fn($value) => strlen(trim((string) $value)) > 0)) === 0) {
                    continue;
                }

                if (!$this->validateCsvRow($row)) {
                    $metrics['skipped']++;
                    if (count($metrics['failed_samples']) < 50) {
                        $metrics['failed_samples'][] = ['line' => $lineNum, 'sku' => trim($row[1] ?? 'UNKNOWN')];
                    }
                    continue;
                }

                $sku = trim($row[1]);
                $existing = $this->productModel->findBySku($sku);

                if ($existing) {
                    $productId = (int)$existing['id'];
                    $updatedData = [
                        'product_name'        => trim($row[0]),
                        'sku'                 => $sku,
                        'description'         => trim($row[2] ?? ''),
                        'category_id'         => (int) $row[3],
                        'price'               => (float) $row[4],
                        'low_stock_threshold' => (int) $row[6],
                        'status'              => 'active'
                    ];

                    $requestedStock = (int) $row[5];
                    $currentStock = (int) $existing['stock_quantity'];

                    if ($requestedStock !== $currentStock) {
                        $stockDiff = $requestedStock - $currentStock;
                        $this->productService->adjustStock(
                            $productId,
                            $stockDiff > 0 ? 'IN' : 'OUT',
                            abs($stockDiff),
                            'CSV bulk import reconciliation',
                            $userId
                        );
                    }

                    $this->productModel->updateProduct($productId, $updatedData);
                } else {
                    $productData = [
                        'product_name'        => trim($row[0]),
                        'sku'                 => $sku,
                        'description'         => trim($row[2] ?? ''),
                        'category_id'         => (int) $row[3],
                        'price'               => (float) $row[4],
                        'stock_quantity'      => (int) $row[5],
                        'low_stock_threshold' => (int) $row[6],
                        'status'              => 'active',
                        'image_path'          => null,
                        'created_by'          => $userId
                    ];

                    $this->productModel->create($productData);
                }

                $metrics['success']++;
            }

            fclose($handle);
            $this->productModel->commit();
            return $metrics;

        } catch (Throwable $e) {
            fclose($handle);
            $this->productModel->rollBack();
            throw $e;
        }
    }

    /**
     * Row level strict layout contract validation
     */
    private function validateCsvRow(array $row): bool
    {
        if (count($row) < 7) return false;
        if (!isset($row[0], $row[1], $row[3], $row[4], $row[5], $row[6])) return false;
        if (empty(trim($row[0])) || empty(trim($row[1]))) return false;
        if (!is_numeric($row[3]) || !is_numeric($row[4])) return false;
        if (!is_numeric($row[5]) || !is_numeric($row[6])) return false;
        return true;
    }
}