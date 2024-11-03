<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\SessionManager;
use App\Core\View;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class WarehouseController extends Controller {
    private ProductModel $productModel;
    private CategoryModel $categoryModel;

    public function __construct(View $view, $services) {
        parent::__construct($view);
        $this->productModel = $services[ProductModel::class];
    }

    public function handleGet(Request $request) {
        $query = $request->getQuery();
        if (!empty($query['id'])) {
            $id = $query['id'];
            $product = $this->productModel->getByIdWithStock($id);

            return $this->view->view("edit_warehouse", [
                'product' => $product,
            ]);
        }

        $products = $this->productModel->getAll();
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $message = $sessionManager->get('message');
        $sessionManager->set('message', '');

        return $this->view->view('warehouse', [
            'products' => $products,
            'message' => $message
        ]);
    }

    public function handlePost(Request $request) {
        $productId = $request->getBody()["id"];
        $warehouseId = $request->getBody()["warehouse_id"];
        $stored = $request->getBody()["stored"];

        if (empty($warehouseId)) {
            $this->productModel->createProductStock($productId, $stored);
        } else {
            $this->productModel->updateWarehouse($warehouseId, $stored);
        }
        
        $sessionManager = new SessionManager();
        $sessionManager->initSession();
        $sessionManager->set('message', 'Product number was changed in warehouse');

        return new RedirectResponse('/warehouse');
    }
}
