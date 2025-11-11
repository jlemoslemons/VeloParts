<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\OrderController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\UserController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\SiteController;
use App\Middleware\AuthMiddleware;
use Symfony\Component\HttpFoundation\Request;

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    
    // --- Site Público e E-commerce ---
    $r->addGroup('/', function (FastRoute\RouteCollector $site) {
        $site->addRoute('GET', '', [SiteController::class, 'index']);
        $site->addRoute('GET', 'category', [SiteController::class, 'category']);
        $site->addRoute('GET', 'product', [SiteController::class, 'product']);
        
        // Carrinho
        $site->addRoute('GET', 'cart', [CartController::class, 'show']);
        $site->addRoute('POST', 'cart/add', [CartController::class, 'add']);
        $site->addRoute('POST', 'cart/update', [CartController::class, 'update']);
        $site->addRoute('POST', 'cart/remove', [CartController::class, 'remove']);
        
        // Checkout (Protegido)
        $site->addRoute('GET', 'checkout', [CheckoutController::class, 'show']);
        $site->addRoute('POST', 'checkout/place', [CheckoutController::class, 'placeOrder']);
        
        // Meus Pedidos (Protegido)
        $site->addRoute('GET', 'my-orders', [SiteController::class, 'myOrders']);
    });

    // --- Autenticação ---
    $r->addGroup('/auth', function (FastRoute\RouteCollector $auth) {
        $auth->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
        $auth->addRoute('GET', '/create', [AuthController::class, 'create']); // Rota de debug do VeloParts
        $auth->addRoute('POST', '/login', [AuthController::class, 'login']);
        $auth->addRoute('POST', '/logout', [AuthController::class, 'logout']);
    });

    // --- Painel de Admin (Protegido) ---
    $r->addGroup('/admin', function (FastRoute\RouteCollector $group) {
        $group->addRoute('GET', '', [AdminController::class, 'index']);

        $group->addGroup('/products', function (FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '', [ProductController::class, 'index']);
            $r->addRoute('GET', '/create', [ProductController::class, 'create']);
            $r->addRoute('POST', '/store', [ProductController::class, 'store']);
            $r->addRoute('GET', '/show', [ProductController::class, 'show']);
            $r->addRoute('GET', '/edit', [ProductController::class, 'edit']);
            $r->addRoute('POST', '/update', [ProductController::class, 'update']);
            $r->addRoute('POST', '/delete', [ProductController::class, 'delete']);
        });

        $group->addGroup('/categories', function (FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '', [CategoryController::class, 'index']);
            $r->addRoute('GET', '/create', [CategoryController::class, 'create']);
            $r->addRoute('POST', '/store', [CategoryController::class, 'store']);
            $r->addRoute('GET', '/show', [CategoryController::class, 'show']);
            $r->addRoute('GET', '/edit', [CategoryController::class, 'edit']);
            $r->addRoute('POST', '/update', [CategoryController::class, 'update']);
            $r->addRoute('POST', '/delete', [CategoryController::class, 'delete']);
        });

        $group->addGroup('/users', function (FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '', [UserController::class, 'index']);
            $r->addRoute('GET', '/create', [UserController::class, 'create']);
            $r->addRoute('POST', '/store', [UserController::class, 'store']);
            $r->addRoute('GET', '/show', [UserController::class, 'show']);
            $r->addRoute('POST', '/delete', [UserController::class, 'delete']);
        });
        
        // Gestão de Pedidos
        $group->addGroup('/orders', function (FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '', [OrderController::class, 'index']);
            $r->addRoute('GET', '/show', [OrderController::class, 'show']);
            $r->addRoute('POST', '/update-status', [OrderController::class, 'updateStatus']);
        });
    });
});

// --- Dispatcher (Lógica de Roteamento) ---
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) $uri = substr($uri, 0, $pos);
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$request = Request::createFromGlobals();

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Rota não encontrada';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Método não permitido';
        break;
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $controller = new $class();

        // Módulos protegidos
        $protectedRoutes = [
            '/admin',
            '/checkout',
            '/my-orders' // Adiciona proteção
        ];

        // Se a rota começar com alguma dessas, exige login
        foreach ($protectedRoutes as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                $redirect = AuthMiddleware::requireLogin();
                if ($redirect) { 
                    \App\Core\Flash::push('warning', 'Você precisa estar logado para acessar esta página.');
                    $redirect->send(); 
                    exit; 
                }
                break;
            }
        }

        $response = $controller->$method($request);
        $response->send();
        break;
}