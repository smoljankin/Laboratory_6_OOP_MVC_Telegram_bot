<?php

namespace App\Core;

class Request {
    private $data;

    public function __construct($requestData) {
        $this->data = $requestData;
    }

    public function getQuery() {
        return $this->data['query'];
    }

    public function getBody() {
        return $this->data['body'];
    }
}
