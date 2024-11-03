<?php

namespace App\Core\Response;

class Response implements IResponse {
    public function __construct(private $body, private $responseCode = 200) {}

    public function send() {
        http_response_code($this->responseCode);
        print $this->body;
        exit();
    }
}
