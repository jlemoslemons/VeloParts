<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\View;
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
        $perPage = 5;
        $total = $this->repo->countAll();
        $orderItems = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);
        $html = $this->view->render('admin/order_items/index', compact('orderItems', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $orders = $this->orderRepo->findAll();
        $products = $this->productRepo->findAll();
        $html = $this->view->render('admin/order_items/create', ['csrf' => Csrf::token(), 'errors' => [], 'orders' => $orders, 'products' => $products]);
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) 
            return new Response('Token CSRF inválido', 419);
        
        $errors = $this->service->validate($request->request->all());
        if ($errors) {
            $orders = $this->orderRepo->findAll();
            $products = $this->productRepo->findAll();
            $html = $this->view->render('admin/order_items/create', ['csrf' => Csrf::token(), 'errors' => $errors, 'old' => $request->request->all(), 'orders' => $orders, 'products' => $products]);
            return new Response($html, 422);
        }
        
        $orderItem = $this->service->make($request->request->all());
        $id = $this->repo->create($orderItem);
        return new RedirectResponse('/admin/order-items/show?id=' . $id);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $orderItem = $this->repo->find($id);
        if (!$orderItem) return new Response('Item do pedido não encontrado', 404);
        $html = $this->view->render('admin/order_items/show', ['orderItem' => $orderItem]);
        return new Response($html);
    }

    public function delete(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) 
            return new Response('Token CSRF inválido', 419);
        
        $id = (int)$request->request->get('id', 0);
        if ($id > 0) $this->repo->delete($id);
        return new RedirectResponse('/admin/order-items');
    }
}