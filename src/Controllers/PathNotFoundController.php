<?php

namespace App\Controllers;

use App\Core\Request;

class PathNotFoundController extends Controller {
    public function handleGet(Request $request) {
        return $this->handleError();
    }

    public function handlePost(Request $request) {
        return $this->handleError();
    }

    public function handlePut(Request $request) {
        return $this->handleError();
    }

    public function handleDelete(Request $request) {
        return $this->handleError();
    }
    
    private function handleError() {
        return "Path is not found";
    }
}
