<?php

namespace App\Controllers\Admin;

use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController
{
    private View $view;
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;
    private OrderRepository $orderRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->orderRepo = new OrderRepository();
    }

    public function index(Request $request): Response
    {
        $totalProducts = $this->productRepo->countAll();
        $totalCategories = $this->categoryRepo->countAll();
        $totalOrders = $this->orderRepo->countAll();

        $html = $this->view->render('admin/index', [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalOrders' => $totalOrders
        ]);
        
        return new Response($html);
    }
}