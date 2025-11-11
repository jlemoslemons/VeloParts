<?php
namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Repositories\OrderRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    private View $view;
    private OrderRepository $repo;
    
    // Status permitidos para atualização
    private array $statuses = ['Pendente', 'Pago', 'Enviado', 'Cancelado'];

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new OrderRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 10;
        $total = $this->repo->countAll();
        $orders = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);
        $html = $this->view->render('admin/orders/index', compact('orders', 'page', 'pages'));
        return new Response($html);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $order = $this->repo->find($id);
        if (!$order) {
            return new Response('Pedido não encontrado', 404);
        }
        
        $items = $this->repo->findItemsByOrderId($id);
        
        $html = $this->view->render('admin/orders/show', [
            'order' => $order,
            'items' => $items,
            'statuses' => $this->statuses, // Para o dropdown
            'csrf' => Csrf::token()
        ]);
        return new Response($html);
    }
    
    public function updateStatus(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }
        
        $id = (int)$request->request->get('id', 0);
        $status = (string)$request->request->get('status', '');
        
        if ($id <= 0 || !in_array($status, $this->statuses)) {
            Flash::push('danger', 'Status inválido.');
            return new RedirectResponse('/admin/orders');
        }
        
        $this->repo->updateStatus($id, $status);
        Flash::push('success', 'Status do pedido atualizado para: ' . $status);
        return new RedirectResponse('/admin/orders/show?id=' . $id);
    }
}