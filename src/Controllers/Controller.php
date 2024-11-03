<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

abstract class Controller {
    protected $view;

    public function __construct(View $view, $services = []) {
        $this->view = $view;
    }

    public function loadModel($model) {
        require_once 'models/' . $model . '.php';
        return new $model();
    }

    public function handleGet(Request $request) {
        throw new \Exception(__FUNCTION__ . " is not implemented.");
    }

    public function handlePost(Request $request) {
        throw new \Exception(__FUNCTION__ . " is not implemented.");
    }

    public function handlePut(Request $request) {
        throw new \Exception(__FUNCTION__ . " is not implemented.");
    }

    public function handleDelete(Request $request) {
        throw new \Exception(__FUNCTION__ . " is not implemented.");
    }

    public function __call($method, $args) {
        $method = ucfirst(strtolower($method));
        return call_user_func_array([$this, 'handle' . $method], $args);
    }
}
