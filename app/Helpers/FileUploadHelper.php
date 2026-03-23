<?php

namespace App\Helpers;

/**
 * Class FileUploadHelper
 *
 * Responsibility:
 * Handle secure product image uploads only.
 */
class FileUploadHelper
{
    /**
     * Maximum allowed image size in bytes (2 MB).
     */
    private const MAX_FILE_SIZE = 2097152;

    /**
     * Allowed MIME types mapped to safe extensions.
     *
     * @var array<string,string>
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
    ];

    /**
     * Upload product image securely.
     *
     * @param array<string,mixed> $file Uploaded file array from $_FILES['image']
     *
     * @return array{
     *     success: bool,
     *     path?: string,
     *     error?: string
     * }
     */
    public static function uploadProductImage(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [
                'success' => true,
                'path'    => null,
            ];
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'error'   => 'Image upload failed. Please try again.',
            ];
        }

        $tmpName = $file['tmp_name'] ?? '';
        $fileSize = (int) ($file['size'] ?? 0);

        if (!is_uploaded_file($tmpName)) {
            return [
                'success' => false,
                'error'   => 'Invalid uploaded file.',
            ];
        }

        if ($fileSize <= 0 || $fileSize > self::MAX_FILE_SIZE) {
            return [
                'success' => false,
                'error'   => 'Image size must be less than or equal to 2 MB.',
            ];
        }

        $mimeType = mime_content_type($tmpName);
        if (!is_string($mimeType) || !isset(self::ALLOWED_MIME_TYPES[$mimeType])) {
            return [
                'success' => false,
                'error'   => 'Only JPG, JPEG, and PNG images are allowed.',
            ];
        }

        $safeExtension = self::ALLOWED_MIME_TYPES[$mimeType];
        $newFileName = uniqid('product_', true) . '.' . $safeExtension;

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/products/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            error_log('[FileUploadHelper] Failed to create upload directory: ' . $uploadDir);

            return [
                'success' => false,
                'error'   => 'Unable to prepare image upload directory.',
            ];
        }

        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            error_log('[FileUploadHelper] Failed to move uploaded file: ' . $newFileName);

            return [
                'success' => false,
                'error'   => 'Failed to save uploaded image.',
            ];
        }

        return [
            'success' => true,
            'path'    => $newFileName,
        ];
    }

    /**
     * Delete an existing product image file safely.
     *
     * @param string|null $fileName Stored file name only
     */
    public static function deleteProductImage(?string $fileName): void
    {
        if (empty($fileName)) {
            return;
        }

        $filePath = dirname(__DIR__, 2) . '/public/uploads/products/' . basename($fileName);

        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }
}