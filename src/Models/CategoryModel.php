<?php

namespace App\Models;

class CategoryModel extends Model {
    public function getAll() {
        $results = $this->db->query(
            'SELECT * FROM category'
        );

        if (empty ($results)) {
            return [];
        }

        return $results;
    }

    public function getById($id) {
        $results = $this->db->queryWithParameters(
            'SELECT * FROM category WHERE id = :id', 
            [
                'id' => $id
            ]
        );

        if (empty($results)) {
            return [];
        }

        return $results[0];
    }

    public function add($name) {
        $results = $this->db->queryWithParameters(
            'INSERT INTO category(name) VALUES(:name)', 
            [
                ':name' => $name
            ]
        );
    }

    public function update($id, $name) {
        $this->db->queryWithParameters(
            'UPDATE category SET name = :name WHERE id = :id', 
            [
                'id' => $id,
                'name' => $name,
            ]
        );
    }

    public function delete($id) {
        $this->db->queryWithParameters(
            'DELETE FROM category WHERE id = :id', 
            [
                'id' => $id
            ]
        );
    }
}