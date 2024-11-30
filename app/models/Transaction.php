<?php

namespace app\models;

class Transaction extends Model {

    protected $table = 'transactions';

    public function saveTransaction($data) {
        try {
            $query = "INSERT INTO $this->table (user_id, cryptocurrency, quantity, price) 
                      VALUES (:user_id, :cryptocurrency, :quantity, :price)";
            $this->query($query, [
                'user_id' => $data['user_id'],
                'cryptocurrency' => $data['cryptocurrency'],
                'quantity' => $data['quantity'],
                'price' => $data['price']
            ]);
            return true;
        } catch (\PDOException $e) {
            error_log("Save Transaction Error: " . $e->getMessage());
            return false;
        }
    }    

    public function getTransactionsByUser($userId) {
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id";
        return $this->query($query, ['user_id' => $userId]);
    }

    public function deleteTransaction($transactionId) {
        return $this->deleteBy('id', $transactionId);
    }

    public function getTransactionSummaryByUser($userId) {
        $query = "
            SELECT cryptocurrency, SUM(quantity) as total_quantity, AVG(price) as average_price
            FROM $this->table
            WHERE user_id = :user_id
            GROUP BY cryptocurrency
        ";
        return $this->query($query, ['user_id' => $userId]);
    }
}
