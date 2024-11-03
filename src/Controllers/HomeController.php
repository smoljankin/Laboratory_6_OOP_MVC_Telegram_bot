<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\SessionManager;

class HomeController extends Controller {
    public function handleGet(Request $request) {
        $sessionManager = new SessionManager();
        $sessionManager->initSession();

        $message = $sessionManager->get('message');
        $sessionManager->set('message', '');

        $loggedInUser = $sessionManager->get('user');

        if (!$loggedInUser) {
            return new RedirectResponse("/login");
        }
        
        return $this->view->view("home", ['message' => $message]);
    }
}
