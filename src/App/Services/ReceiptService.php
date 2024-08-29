<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;

class ReceiptService
{
  public function __construct(private Database $db) {}

  public function ValidateFile(?array $file)
  {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
      throw new ValidationException([
        'receipt' => ['Failed to upload file']
      ]);
    }

    $maxFileSizeMB = 3 * 1024 * 1024;

    if ($file['size'] > $maxFileSizeMB) {
      throw new ValidationException([
        'receipt' => ['Fail upload is too large']
      ]);
    }

    $originalFileName = $file['name'];

    if (!preg_match('/^[A-Za-z0-9\s._-]+$/', $originalFileName)) {
      throw new ValidationException([
        'receipt' => ['Invalid filename']
      ]);
    }

    $clientMimeType = $file['type'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

    if (!in_array($clientMimeType, $allowedMimeTypes)) {
      throw new ValidationException([
        'receipt' => ['Invalid file type']
      ]);
    }
  }

  public function upload(array $file)
  {
    $fileExtention = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = bin2hex(random_bytes(16)) . "." . $fileExtention;

    $uploadPath = Paths::STORAGE_UPLOADS . "/" . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
      throw new ValidationException([
        'receipt' => ['Failed to upload file']
      ]);
    }
  }
}
