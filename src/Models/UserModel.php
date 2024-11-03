<?php

namespace App\Models;
class UserModel extends Model {
    public function getUserByName($name) {
        $results = $this->db->queryWithParameters(
            'SELECT * FROM users WHERE name = :name', 
            [
                'name' => $name
            ]
        );

        if (empty ($results)) {
            return ['name' => '', 'password' => ''];
        }

        return $results[0];
    }
}
