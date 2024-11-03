<?php

namespace App\Models;

use App\Core\DbConnection;

class Model {
    public function __construct(protected DbConnection $db) {}
}
