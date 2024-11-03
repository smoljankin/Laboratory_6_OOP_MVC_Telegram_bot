<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\Response\Response;
use App\Core\SessionManager;
use App\Core\View;
use App\Models\UserModel;

class LoginController extends Controller {
    private UserModel $userModel;

    public function __construct(View $view, $services) {
        parent::__construct($view);
        $this->userModel = $services[UserModel::class];
    }

    public function handleGet(Request $request): Response {
        $sessionManager = new SessionManager();
        $sessionManager->initSession();

        $message = $sessionManager->get('message');
        $sessionManager->set('message', '');
        
        return $this->view->view("login", ['message' => $message]);
    }

    public function handlePost(Request $request): RedirectResponse {
        $userName = $request->getBody()["name"];
        $password = $request->getBody()["password"];

        $user = $this->userModel->getUserByName($userName);

        $sessionManager = new SessionManager();
        $sessionManager->initSession();

        if (password_verify($password, $user['password'])) {
            $sessionManager->set('message', 'User is logged in');
            $sessionManager->set('user', true);
            return new RedirectResponse("/");
        }

        $sessionManager->set("message", "Invalid credentials");
        return new RedirectResponse("/login");
    }
}
