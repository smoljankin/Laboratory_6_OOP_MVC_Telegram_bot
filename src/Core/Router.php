<?php

namespace App\Core;

use App\Core\Response\IResponse;

class Router {

    private $routes = [];
    private $config = [];
    private $models = [];
    private $view;

    public function __construct($routesFile, $configFile) {
        $this->routes = require_once($routesFile);
        $this->config = require_once($configFile);
        $this->view = new View($this->config['templateDir']);
        $dbConnection = new DbConnection($this->config['dbFile']);
        $this->models = $this->prepareModels($this->config['models'], $dbConnection);
    }

    public function handle() {
        $parsedUrl = ['path' => '/'];
        if (!empty($_SERVER['REQUEST_URI'])) {
            $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
        }

        $path = $parsedUrl['path'];
        $method = $_SERVER['REQUEST_METHOD'];
        $response = null;

        foreach ($this->routes as $route) {
            if ($route['uri'] == $path) {
                $response = $this->tryRunHandler($route['handler'], $method);
                break;
            }
        }

        if (is_null($response)) {
            $response = $this->tryRunHandler(\App\Controllers\PathNotFoundController::class, $method);
        }

        $this->execute($response);
    }

    private function tryRunHandler($handlerName, $method): string|IResponse {
        $handler = new $handlerName($this->view, $this->models);
        return $handler->$method($this->createRequest());
    }

    private function createRequest() {
        $bodyRawString = file_get_contents('php://input');
        $body = [];
        parse_str($bodyRawString, $body);
        
        $requestData = [
            'query' => $_GET,
            'body' => $body
        ];
        
        return new Request($requestData);
    }

    private function execute($response) {
        if ($response instanceof IResponse) {
            $response->send();
            return;
        }
    
        print $response;
    }

    private function prepareModels(array $models, DbConnection $db) {
        $initModels = [];
        foreach ($models as $model) {
            $initModels[$model] = new $model($db);
        }
        return $initModels;
    }
}
