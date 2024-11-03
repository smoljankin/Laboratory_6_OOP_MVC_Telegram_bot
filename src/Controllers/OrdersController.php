<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response\RedirectResponse;
use App\Core\View;
use App\Models\OrderModel;

class OrdersController extends Controller {
    private OrderModel $orderModel;

    public function __construct(View $view, $services) {
        parent::__construct($view);
        $this->orderModel = $services[OrderModel::class];
    }

    public function handleGet(Request $request) {
        if (!empty($request->getQuery())) {
            $orderId = $request->getQuery()['id'];
            $orderItems = $this->orderModel->getById($orderId);
            $order = $this->convertOrder($orderItems);

            if (empty($order)) {
                return new RedirectResponse('/orders');
            }

            return $this->view->view('order', [
                'order' => $order,
            ]);
        }

        $orders = $this->orderModel->getAll();

        return $this->view->view('orders', [
            'orders' => $orders,
        ]);
    }

    private function convertOrder($orderItems) {
        if (empty($orderItems)) {
            return [];
        }

        $orderData = [
            'id' => $orderItems[0]['id'],
            'email' => $orderItems[0]['email'],
        ];

        $orderDetails = [];
        $total = 0;

        foreach ($orderItems as $orderItem) {
            $orderDetails[] = [
                'count' => $orderItem['count'],
                'product_name' => $orderItem['product_name'],
                'price_per_one' => $orderItem['price'],
                'price' => $orderItem['price'] * $orderItem['count'],
            ];
            $total += $orderItem['price'] * $orderItem['count'];
        }

        $orderData['total'] = $total;
        $orderData['details'] = $orderDetails;

        return $orderData;
    }
}