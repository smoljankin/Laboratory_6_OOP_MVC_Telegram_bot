<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\SessionManager;
use App\Core\View;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class ProductsController extends Controller {
    private ProductModel $productModel;
    private CategoryModel $categoryModel;

    public function __construct(View $view, $services) {
        parent::__construct($view);
        $this->productModel = $services[ProductModel::class];
        $this->categoryModel = $services[CategoryModel::class];
    }

    public function handleGet(Request $request) {
        $query = $request->getQuery();
        if (!empty($query['id'])) {
            $id = $query['id'];
            $product = $this->productModel->getById($id);

            return $this->view->view("edit_product", [
                'product' => $product,
            ]);
        }

        if (empty($query['category'])) {
            return new RedirectResponse('/category');
        }

        $categoryId = $query['category'];
        $category = $this->categoryModel->getById($categoryId);
        $products = $this->productModel->getAllForCategory($categoryId);
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $message = $sessionManager->get('message');
        $sessionManager->set('message', '');

        return $this->view->view('products', [
            'products' => $products,
            'category' => $category,
            'message' => $message
        ]);
    }

    public function handlePost(Request $request) {
        $id = $request->getBody()["id"];
        $action = $request->getBody()["action"];

        if ($action === 'put') {
            return $this->productUpdate($id, $request->getBody());
        }

        if ($action === 'switch_status') {
            return $this->productSwitchStatus($id, $request->getBody()['category_id']);
        }

        if ($action === 'delete') {
            return $this->productDelete($id, $request->getBody()['category_id']);
        }

        return new RedirectResponse('/category');
    }

    private function productUpdate($id, $params) {
        $this->productModel->update($id, $params['name'], $params['description'], $params['price']);
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Category was updated');

        return new RedirectResponse('/products?category=' . $params['category_id']);
    }

    private function productSwitchStatus($id, $categoryId) {
        $product = $this->productModel->getById($id);
        $this->productModel->updateStatus($id, !$product['status']);
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Status was changed');

        return new RedirectResponse('/products?category=' . $categoryId);
    }

    private function productDelete($id, $categoryId) {
        $this->productModel->delete($id);
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Product was removed');

        return new RedirectResponse('/products?category=' . $categoryId);
    }
}
