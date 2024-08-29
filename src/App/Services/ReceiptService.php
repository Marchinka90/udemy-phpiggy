<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

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

    if (!preg_match('^/[A-zz-Z0-9\s._-]+$/', $originalFileName)) {
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

  // public function getUserTransactions(int $length, int $offset)
  // {
  //   $searchTerm = addcslashes($_GET['s'] ?? '', '%_');
  //   $params = [
  //     ':user_id' => $_SESSION['user'],
  //     ':description' => "%{$searchTerm}%"
  //   ];

  //   $transactions = $this->db->query(
  //     "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date 
  //     FROM transactions 
  //     WHERE user_id = :user_id
  //     AND description LIKE :description
  //     LIMIT {$length} OFFSET {$offset}",
  //     $params
  //   )->findAll();

  //   $transactionCount = $this->db->query(
  //     "SELECT COUNT(*)
  //     FROM transactions 
  //     WHERE user_id = :user_id
  //     AND description LIKE :description",
  //     $params
  //   )->count();

  //   return [$transactions, $transactionCount];
  // }

  // public function getUserTransaction(string $id)
  // {
  //   return $this->db->query(
  //     "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date  
  //     FROM transactions 
  //     WHERE user_id = :user_id AND id = :id",
  //     [
  //       'id' => $id,
  //       'user_id' => $_SESSION['user']
  //     ]
  //   )->find();
  // }

  // public function update(array $formData, int $id)
  // {
  //   $formattedDate = "{$formData['date']} 00:00:00";

  //   $this->db->query(
  //     "UPDATE transactions
  //     SET description = :description, 
  //       amount = :amount, 
  //       date = :date
  //     WHERE id = :id
  //     AND user_id = :user_id",
  //     [
  //       'description' => $formData['description'],
  //       'amount' => $formData['amount'],
  //       'user_id' => $_SESSION['user'],
  //       'date' => $formattedDate,
  //       'id' => $id
  //     ]
  //   );
  // }

  // public function delete(int $id)
  // {
  //   $this->db->query(
  //     "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
  //     [
  //       'id' => $id,
  //       'user_id' => $_SESSION['user']
  //     ]
  //   );
  // }
}
