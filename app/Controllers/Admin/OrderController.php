<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\View;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Services\OrderService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrderController
{
    private View $view;
    private OrderRepository $repo;
    private OrderItemRepository $itemRepo;
    private ProductRepository $productRepo;
    private OrderService $service;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new OrderRepository();
        $this->itemRepo = new OrderItemRepository();
        $this->productRepo = new ProductRepository();
        $this->service = new OrderService();
        $this->userRepo = new UserRepository();
    }

    public function index(Request $request): Response
    {
        try {
            $page = max(1, (int)$request->query->get('page', 1));
            $perPage = 10;
            $total = $this->repo->countAll();
            $orders = $this->repo->paginate($page, $perPage);
            $pages = (int)ceil($total / $perPage);

            $html = $this->view->render('admin/orders/index', compact('orders', 'page', 'pages'));
            return new Response($html);
        } catch (Throwable $e) {
            return new Response("Erro ao carregar pedidos: " . $e->getMessage(), 500);
        }
    }

    public function create(): Response
    {
        try {
            $users = $this->userRepo->paginate(1, 500);
            $html = $this->view->render('admin/orders/create', [
                'csrf' => Csrf::token(), 
                'errors' => [], 
                'users' => $users,
                'old' => []
            ]);
            return new Response($html);
        } catch (Throwable $e) {
            return new Response("Erro ao abrir formulário: " . $e->getMessage(), 500);
        }
    }

    public function store(Request $request): Response
    {
        try {
            if (!Csrf::validate($request->request->get('_csrf'))) {
                return new Response('Token CSRF inválido', 419);
            }

            $data = $request->request->all();
            $data['total_amount'] = 0.00; // Força zero inicial

            $errors = $this->service->validate($data);
            
            if ($errors) {
                $users = $this->userRepo->paginate(1, 500);
                $html = $this->view->render('admin/orders/create', [
                    'csrf' => Csrf::token(),
                    'errors' => $errors,
                    'old' => $data,
                    'users' => $users
                ]);
                return new Response($html, 422);
            }

            $order = $this->service->make($data);
            $id = $this->repo->create($order);
            
            return new RedirectResponse('/admin/orders/show?id=' . $id);

        } catch (Throwable $e) {
            return new Response("Erro CRÍTICO ao salvar: " . $e->getMessage(), 500);
        }
    }

    public function show(Request $request): Response
    {
        try {
            $id = (int)$request->query->get('id', 0);
            $order = $this->repo->find($id);

            if (!$order) {
                return new Response('Pedido não encontrado', 404);
            }

            $items = $this->itemRepo->findByOrderId($id);

            // --- CRÍTICO: Carrega o mapa de produtos para a View ---
            $productsMap = [];
            $allProducts = $this->productRepo->findAll(); 
            foreach ($allProducts as $p) {
                $productsMap[$p['id']] = $p;
            }

            $user = $this->userRepo->find($order['user_id']);

            $html = $this->view->render('admin/orders/show', [
                'order' => $order,
                'items' => $items,
                'productsMap' => $productsMap, // Envia a variável
                'user' => $user
            ]);
            return new Response($html);
        } catch (Throwable $e) {
            return new Response("Erro ao exibir detalhes: " . $e->getMessage(), 500);
        }
    }

    public function updateStatus(Request $request): Response
    {
        try {
            if (!Csrf::validate($request->request->get('_csrf'))) {
                return new Response('Token CSRF inválido', 419);
            }

            $id = (int)$request->request->get('id');
            $newStatus = $request->request->get('status');

            $orderData = $this->repo->find($id);
            if (!$orderData) {
                return new Response('Pedido não encontrado', 404);
            }

            $order = new \App\Models\Order(
                (int)$orderData['order_id'],
                (int)$orderData['user_id'],
                $orderData['order_date'],
                $newStatus,
                (float)$orderData['total_amount']
            );

            $this->repo->update($order);

            return new RedirectResponse('/admin/orders/show?id=' . $id);

        } catch (Throwable $e) {
            return new Response("Erro ao atualizar status: " . $e->getMessage(), 500);
        }
    }

    public function delete(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }

        $id = (int)$request->request->get('id', 0);
        if ($id > 0) {
            $this->repo->delete($id);
        }
        return new RedirectResponse('/admin/orders');
    }
}