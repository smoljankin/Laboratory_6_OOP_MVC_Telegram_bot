<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\SessionManager;
use App\Core\View;
use App\Models\CategoryModel;

class NewCategoryController extends Controller {
    private CategoryModel $categoryModel;

    public function __construct(View $view, $services) {
        parent::__construct($view);
        $this->categoryModel = $services[CategoryModel::class];
    }

    public function handleGet(Request $request) {
        $categories = $this->categoryModel->getAll();

        return $this->view->view("new_category", ["categories"=> $categories]);
    }

    public function handlePost(Request $request) {
        $name = $request->getBody()['name'];
        $this->categoryModel->add($name);

        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Category was created');

        return new RedirectResponse('/category');
    }
}
