<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\View;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Services\OrderService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    private View $view;
    private OrderRepository $repo;
    private OrderService $service;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new OrderRepository();
        $this->service = new OrderService();
        $this->userRepo = new UserRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 5;
        $total = $this->repo->countAll();
        $orders = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);
        $html = $this->view->render('admin/orders/index', compact('orders', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $users = $this->userRepo->findAll();
        $html = $this->view->render('admin/orders/create', ['csrf' => Csrf::token(), 'errors' => [], 'users' => $users]);
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) 
            return new Response('Token CSRF inválido', 419);
        
        $errors = $this->service->validate($request->request->all());
        if ($errors) {
            $users = $this->userRepo->findAll();
            $html = $this->view->render('admin/orders/create', ['csrf' => Csrf::token(), 'errors' => $errors, 'old' => $request->request->all(), 'users' => $users]);
            return new Response($html, 422);
        }
        
        $order = $this->service->make($request->request->all());
        $id = $this->repo->create($order);
        return new RedirectResponse('/admin/orders/show?id=' . $id);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $order = $this->repo->find($id);
        if (!$order) return new Response('Pedido não encontrado', 404);
        $html = $this->view->render('admin/orders/show', ['order' => $order]);
        return new Response($html);
    }

    public function delete(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) 
            return new Response('Token CSRF inválido', 419);
        
        $id = (int)$request->request->get('id', 0);
        if ($id > 0) $this->repo->delete($id);
        return new RedirectResponse('/admin/orders');
    }
}