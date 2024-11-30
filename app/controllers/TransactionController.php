<?php

namespace app\controllers;

use app\models\Transaction;

class TransactionController extends Controller {
    /**
     * Get transactions for a user (returns JSON).
     */
    public function getTransactions() {
        if (!$this->isAuthenticated()) {
            $this->returnJSON(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $transactionModel = new Transaction();
        $userId = $_SESSION['user_id']; // Use session-based user ID
        $transactions = $transactionModel->getTransactionsByUser($userId);
        $this->returnJSON($transactions ?: ['status' => 'error', 'message' => 'No transactions found']);
    }

    /**
     * Save a new transaction (returns JSON).
     */
    public function saveTransaction() {
        if (!$this->isAuthenticated()) {
            $this->returnJSON(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $transactionModel = new Transaction();
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            isset($data['cryptocurrency'], $data['quantity'], $data['price']) &&
            !empty($data['cryptocurrency']) &&
            is_numeric($data['quantity']) &&
            is_numeric($data['price'])
        ) {
            $data['user_id'] = $_SESSION['user_id']; // Use session-based user ID
            $saved = $transactionModel->saveTransaction($data);
            $this->returnJSON($saved ? ['status' => 'success', 'message' => 'Transaction saved'] : ['status' => 'error', 'message' => 'Save failed']);
        } else {
            $this->returnJSON(['status' => 'error', 'message' => 'Invalid input']);
        }
    }

    /**
     * transaction summary (returns JSON).
     */
    public function getTransactionSummary() {
        if (!$this->isAuthenticated()) {
            $this->returnJSON(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $transactionModel = new Transaction();
        $userId = $_SESSION['user_id']; // Use session-based user ID
        $summary = $transactionModel->getTransactionSummaryByUser($userId);
        $this->returnJSON($summary ?: ['status' => 'error', 'message' => 'No summary data found']);
    }

    /**
     * Delete a transaction by its ID.
     */
    public function deleteTransaction() {
        if (!$this->isAuthenticated()) {
            $this->returnJSON(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $transactionModel = new Transaction();
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['transaction_id']) && is_numeric($data['transaction_id'])) {
            $deleted = $transactionModel->deleteTransaction($data['transaction_id']);
            $this->returnJSON($deleted ? ['status' => 'success', 'message' => 'Deleted successfully'] : ['status' => 'error', 'message' => 'Delete failed']);
        } else {
            $this->returnJSON(['status' => 'error', 'message' => 'Invalid transaction ID']);
        }
    }

    /**
     * transactions table view.
     */
    public function transactionsView() {
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            exit();
        }

        $this->returnView('transaction/transactions.html', true); // Use public view path
    }
}
