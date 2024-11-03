<?php

namespace App\Core;

use PDO;

class DbConnection {
    private $db;
    public function __construct($dbFile) {
        $this->db = new \PDO('sqlite:' . $dbFile);
    }

    public function query($query) {
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryWithParameters($query, $parameters): array {
        $sth = $this->db->prepare($query);
        $sth->execute($parameters);
        $result = $sth->fetchAll();
        return $result;
    }

    public function insert($query, $parameters): int {
        $sth = $this->db->prepare($query);
        $sth->execute($parameters);
        
        return $this->db->lastInsertId();
    }
}
