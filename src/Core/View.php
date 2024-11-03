<?php

namespace App\Core;

use App\Core\Response\Response;

class View {
    private $templateDir;

    public function __construct($templateDir) {
        $this->templateDir = __DIR__ . '/../' . $templateDir;
    }

    public function view($template, $data = []): Response {
        $output = $this->render($template, $data);
        return new Response($output);
    }

    private function render($view, $data): string {
        extract($data);

        ob_start();
        require_once $this->templateDir . '/internal/header.php';
        require_once $this->templateDir . '/' . $view . '.php';
        require_once $this->templateDir . '/internal/footer.php';

        return strval(ob_get_clean());
    }
}
