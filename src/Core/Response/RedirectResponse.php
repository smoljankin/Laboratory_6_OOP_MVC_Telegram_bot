<?php

namespace App\Core\Response;

class RedirectResponse implements IResponse {
    private const RESPONSE_CODE = 303;
    private $url;

    public function __construct(string $url) {
        $this->url = $url;
    }

    public function send() {
        header('Location: ' . $this->url, true, self::RESPONSE_CODE);
    }
}
