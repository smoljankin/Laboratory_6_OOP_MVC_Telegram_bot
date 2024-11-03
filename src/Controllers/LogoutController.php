<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\SessionManager;

class LogoutController extends Controller {
    public function handleGet(Request $request) {
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->terminate();
        return new RedirectResponse("/login");
    }
}
