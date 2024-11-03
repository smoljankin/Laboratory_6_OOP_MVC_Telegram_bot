<?php

namespace App\Models;

class OrderModel extends Model {
    public function getAll() {
        $results = $this->db->query(
            'SELECT * FROM orders'
        );

        if (empty ($results)) {
            return [];
        }

        return $results;
    }

    public function getById($orderId) {
        $results = $this->db->queryWithParameters('
            SELECT o.id id, o.user_email email, o.user_address address, o.user_name name, o.user_phone phone,
                oi.count count, p.name product_name, p.price price  
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN product p ON p.id = oi.product_id
            WHERE o.id = :id
        ', [':id' => $orderId]);

        if (empty ($results)) {
            return [];
        }

        return $results;
    }

    public function getAllByUserId($userId) {
        $results = $this->db->queryWithParameters('
            SELECT *  
            FROM orders o
            WHERE o.user_id = :id
        ', [':id' => $userId]);

        if (empty ($results)) {
            return [];
        }

        return $results;
    }

    public function createOrder($email, $name, $address, $phone, $productId, $num, $userId) {
        $orderId = $this->db->insert('
            INSERT INTO orders (user_email, user_name, user_address, user_phone, user_id)
            VALUES (:email, :name, :address, :phone, :user)
        ', [
            'email'=> $email,
            'name' => $name,
            'address' => $address,
            'phone' => $phone,
            'user' => $userId
        ]);

        $this->db->insert('
            INSERT INTO order_items (count, order_id, product_id)
            VALUES (:count, :order, :product)
        ', [
            'count' => $num,
            'order' => $orderId,
            'product' => $productId
        ]);

        $warehouseData = $this->db->queryWithParameters('
            SELECT count_reserved
            FROM warehouse 
            WHERE product_id = :product
        ', 
            [
            'product' => $productId
        ]);

        $reserved = $warehouseData['count_reserved'] ?? 0;

        $this->db->queryWithParameters('
            UPDATE warehouse 
            SET count_reserved = :count
            WHERE product_id = :product
        ', [
            'count' => $reserved + $num,
            'product' => $productId
        ]);
    }
}
