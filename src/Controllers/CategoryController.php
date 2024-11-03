<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\SessionManager;
use App\Core\View;
use App\Models\CategoryModel;

class CategoryController extends Controller {
    private CategoryModel $categoryModel;

    public function __construct(View $view, $services) {
        parent::__construct($view);
        $this->categoryModel = $services[CategoryModel::class];
    }

    public function handleGet(Request $request) {
        if (!empty($request->getQuery())) {
            $id = $request->getQuery()['id'];
            $category = $this->categoryModel->getById($id);

            return $this->view->view("edit_category", [
                'category' => $category,
            ]);
        }

        $categories = $this->categoryModel->getAll();
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $message = $sessionManager->get('message');
        $sessionManager->set('message', '');

        return $this->view->view("categories", [
            "categories" => $categories,
            'message' => $message
        ]);
    }

    public function handlePost(Request $request) {
        $id = $request->getBody()["id"];
        $action = $request->getBody()["action"];

        if ($action === 'put') {
            $name = $request->getBody()["name"];
            return $this->categoryUpdate($id, $name);
        }

        if ($action === 'delete') {
            return $this->categoryDelete($id);
        }

        return new RedirectResponse('/category');
    }

    private function categoryUpdate($id, $name) {
        $this->categoryModel->update($id, $name);
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Category was updated');

        return new RedirectResponse('/category');
    }


    private function categoryDelete($id) {
        $this->categoryModel->delete($id);
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Category was removed');

        return new RedirectResponse('/category');
    }
}
