<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\View;
use App\Models\Order;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\OrderItemService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderItemController
{
    private View $view;
    private OrderItemRepository $repo;
    private OrderItemService $service;
    private OrderRepository $orderRepo;
    private ProductRepository $productRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new OrderItemRepository();
        $this->service = new OrderItemService();
        $this->orderRepo = new OrderRepository();
        $this->productRepo = new ProductRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 10;
        $total = $this->repo->countAll();
        $orderItems = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);

        $html = $this->view->render('admin/ordersItems/index', compact('orderItems', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $orders = $this->orderRepo->findAll();
        $products = $this->productRepo->findAll();
        
        $html = $this->view->render('admin/ordersItems/create', [
            'csrf' => Csrf::token(), 
            'errors' => [], 
            'orders' => $orders, 
            'products' => $products
        ]);
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }
        
        $data = $request->request->all(); // Pega todos os dados
        $errors = $this->service->validate($data);
        
        // 1. Verificar se o produto existe e buscar o preço
        $product = $this->productRepo->find((int)$data['product_id']);
        if (!$product) {
            $errors['product_id'] = 'Produto não encontrado.';
        }
        
        if ($errors) {
            $orders = $this->orderRepo->findAll();
            $products = $this->productRepo->findAll();
            
            $html = $this->view->render('admin/ordersItems/create', [
                'csrf' => Csrf::token(), 
                'errors' => $errors, 
                'old' => $data, 
                'orders' => $orders, 
                'products' => $products
            ]);
            return new Response($html, 422);
        }
        
        // 2. Injeta o preço do produto no array de dados (sobrescrevendo o que vier do formulário)
        $data['unit_price'] = (float)$product['price'];

        $orderItem = $this->service->make($data);
        $this->repo->create($orderItem);

        $this->updateOrderTotal($orderItem->order_id);
        
        return new RedirectResponse('/admin/orders/show?id=' . $orderItem->order_id);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $orderItem = $this->repo->find($id);
        
        if (!$orderItem) {
            return new Response('Item do pedido não encontrado', 404);
        }
        
        $html = $this->view->render('admin/ordersItems/show', ['orderItem' => $orderItem]);
        return new Response($html);
    }

    public function delete(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }
        
        $id = (int)$request->request->get('id', 0);
        
        $item = $this->repo->find($id);
        $orderId = $item ? $item['order_id'] : null;

        if ($id > 0) {
            $this->repo->delete($id);
        }

        if ($orderId) {
            $this->updateOrderTotal($orderId);
            return new RedirectResponse('/admin/orders/show?id=' . $orderId);
        }
        
        return new RedirectResponse('/admin/orders');
    }

    private function updateOrderTotal(int $orderId): void
    {
        $orderData = $this->orderRepo->find($orderId);
        if (!$orderData) return;

        $items = $this->repo->findByOrderId($orderId);

        $newTotal = 0.0;
        foreach ($items as $item) {
            // Garante o casting explícito para floats
            $quantity = (float)$item['quantity'];
            $unitPrice = (float)$item['unit_price'];
            $newTotal += ($quantity * $unitPrice);
        }

        $order = new Order(
            (int)$orderData['order_id'],
            (int)$orderData['user_id'],
            $orderData['order_date'],
            $orderData['status'],
            $newTotal
        );

        $this->orderRepo->update($order);
    }
}