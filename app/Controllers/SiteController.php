<?php
namespace App\Controllers;

use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteController
{
    private View $view;
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    // Home Page: Listar produtos recentes
    public function index(Request $request): Response
    {
        $products = $this->productRepo->findRecent(8);
        $html = $this->view->render('site/index', compact('products'));
        return new Response($html);
    }

    // Página de Categoria
    public function category(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $category = $this->categoryRepo->find($id);
        if (!$category) {
            return new Response('Categoria não encontrada', 404);
        }
        $products = $this->productRepo->findAllByCategoryId($id);
        $html = $this->view->render('site/category', compact('products', 'category'));
        return new Response($html);
    }

    // Página de Produto
    public function product(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $product = $this->productRepo->find($id);
        if (!$product) {
            return new Response('Produto não encontrado', 404);
        }
        $html = $this->view->render('site/product', compact('product'));
        return new Response($html);
    }

    // Página "Meus Pedidos"
    public function myOrders(Request $request): Response
    {
        // A autenticação é tratada no routes.php
        $user = AuthService::user();
        $orders = (new \App\Repositories\OrderRepository())->findByUserId((int)$user['id']);
        
        $html = $this->view->render('site/my_orders', compact('orders'));
        return new Response($html);
    }
}