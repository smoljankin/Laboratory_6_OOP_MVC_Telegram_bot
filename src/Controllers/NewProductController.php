<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\SessionManager;
use App\Core\View;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class NewProductController extends Controller {
    private ProductModel $productModel;
    private CategoryModel $categoryModel;

    public function __construct(View $view, $services) {
        parent::__construct($view);
        $this->productModel = $services[ProductModel::class];
        $this->categoryModel = $services[CategoryModel::class];
    }

    public function handleGet(Request $request) {
        $id = $request->getQuery()['category'];
        return $this->view->view("new_product", ['category' => $id]);
    }

    public function handlePost(Request $request) {
        $name = $request->getBody()['name'];
        $desription = $request->getBody()['description'];
        $price = $request->getBody()['price'];
        $category = $request->getBody()['category'];
        $this->productModel->add($name, $desription, $price, $category);

        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Product was created');

        return new RedirectResponse('/products?category=' . $category);
    }
}
