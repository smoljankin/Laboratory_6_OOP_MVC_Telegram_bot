<?php

namespace App\Models;

class ProductModel extends Model {
    public function getAllForCategory($categoryId) {
        $results = $this->db->queryWithParameters(
            'SELECT * FROM product WHERE category_id = :category',
            [
                ':category'=> $categoryId
            ]
        );

        if (empty ($results)) {
            return [];
        }

        return $results;
    }

    public function getAll() {
        $results = $this->db->query('
            SELECT product.id id, product.name name, product.description desc, product.price price, 
                category.name category, warehouse.count_reserved reserved, warehouse.count_stored stored  
            FROM product
            LEFT JOIN warehouse ON warehouse.product_id = product.id
            INNER JOIN category ON category.id = product.category_id
        ');

        if (empty ($results)) {
            return [];
        }

        return $results;
    }

    public function getActiveForCategory($categoryId) {
        $results = $this->db->queryWithParameters(
            'SELECT * FROM product WHERE category_id = :category AND status = 1',
            [
                ':category'=> $categoryId
            ]
        );

        if (empty ($results)) {
            return [];
        }

        return $results;
    }

    public function getById($id) {
        $results = $this->db->queryWithParameters(
            'SELECT * FROM product WHERE id = :id', 
            [
                'id' => $id
            ]
        );

        if (empty($results)) {
            return [];
        }

        return $results[0];
    }

    public function getByIdWithStock($id) {
        $results = $this->db->queryWithParameters(
            '
            SELECT product.id id, product.name name, warehouse.count_stored stored, warehouse.id warehouse_id
            FROM product
            LEFT JOIN warehouse ON warehouse.product_id = product.id 
            WHERE product.id = :id', 
            [
                ':id' => $id
            ]
        );

        if (empty($results)) {
            return [];
        }

        return $results[0];
    }

    public function getFullInfoById($id) {
        $results = $this->db->queryWithParameters(
            '
            SELECT product.id id, product.name name, product.description desc, product.price price,
                (warehouse.count_stored - warehouse.count_reserved) count_available
            FROM product
            LEFT JOIN warehouse ON warehouse.product_id = product.id 
            WHERE product.id = :id', 
            [
                ':id' => $id
            ]
        );

        if (empty($results)) {
            return [];
        }

        return $results[0];
    }

    public function add($name, $description, $price, $categoryId) {
        $this->db->queryWithParameters(
            'INSERT INTO product(name, description, price, status, category_id) 
                VALUES(:name, :desc, :price, 1, :category)', 
            [
                ':name' => $name,
                ':desc' => $description,
                ':price' => $price,
                ':category' => $categoryId,
            ]
        );
    }

    public function update($id, $name, $description, $price) {
        $this->db->queryWithParameters(
            '
                UPDATE product 
                SET name = :name, description = :desc, price = :price 
                WHERE id = :id', 
            [
                ':id' => $id,
                ':name' => $name,
                ':desc' => $description,
                ':price' => $price
            ]
        );
    }

    public function updateStatus($id, $status) {
        $this->db->queryWithParameters(
            '
                UPDATE product 
                SET status = :status 
                WHERE id = :id', 
            [
                ':id' => $id,
                ':status' => $status
            ]
        );
    }

    public function delete($id) {
        $this->db->queryWithParameters(
            'DELETE FROM product WHERE id = :id', 
            [
                ':id' => $id
            ]
        );
    }

    public function createProductStock($productId, $stored) {
        $this->db->queryWithParameters(
            'INSERT INTO warehouse(count_reserved, count_stored, product_id) 
                VALUES(0, :stored, :product)', 
            [
                ':stored' => $stored,
                ':product' => $productId,
            ]
        );
    }

    public function updateWarehouse($warehouseId, $stored) {
        $this->db->queryWithParameters(
            '
                UPDATE warehouse 
                SET count_stored = :stored 
                WHERE id = :id', 
            [
                ':id' => $warehouseId,
                ':stored' => $stored
            ]
        );
    }
}