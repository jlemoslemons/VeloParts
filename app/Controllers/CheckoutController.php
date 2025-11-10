<?php
namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController
{
    private View $view;
    private OrderRepository $orderRepo;
    private ProductRepository $productRepo;

    public function __construct()
    {
        Csrf::ensureSession();
        $this->view = new View();
        $this->orderRepo = new OrderRepository();
        $this->productRepo = new ProductRepository();
    }

    // Mostrar página de checkout (resumo)
    public function show(): Response
    {
        // Autenticação tratada em routes.php
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            Flash::push('info', 'Seu carrinho está vazio.');
            return new RedirectResponse('/cart');
        }

        // Re-buscar produtos para garantir preços e totais corretos
        $products = [];
        $total = 0.0;
        $dbProducts = $this->productRepo->findIn(array_keys($cart));

        foreach ($dbProducts as $p) {
            $quantity = $cart[$p['id']];
            $total += $p['price'] * $quantity;
            $products[] = $p; // Passa o produto inteiro para a view
        }
        
        $html = $this->view->render('site/checkout', compact('products', 'cart', 'total'));
        return new Response($html);
    }

    // Processar o pedido
    public function placeOrder(Request $request): Response
    {
        // Autenticação tratada em routes.php
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }

        $user = AuthService::user();
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            return new RedirectResponse('/cart');
        }

        // 1. Validar estoque e calcular total
        $total = 0.0;
        $dbProducts = $this->productRepo->findIn(array_keys($cart));
        $productsById = array_column($dbProducts, null, 'id');

        foreach ($cart as $id => $quantity) {
            if (!isset($productsById[$id])) {
                Flash::push('danger', 'Um produto no seu carrinho não está mais disponível.');
                unset($_SESSION['cart'][$id]);
                return new RedirectResponse('/cart');
            }
            
            $product = $productsById[$id];
            
            if ($quantity > $product['stock']) {
                Flash::push('danger', 'Estoque insuficiente para ' . $product['name'] . '. Disponível: ' . $product['stock']);
                $_SESSION['cart'][$id] = $product['stock']; // Corrige o carrinho
                return new RedirectResponse('/cart');
            }
            
            $total += $product['price'] * $quantity;
        }

        // 2. Criar o Pedido (Order)
        $order = new Order(null, (int)$user['id'], $total, 'Pendente');
        $orderId = $this->orderRepo->create($order);

        // 3. Criar os Itens do Pedido (OrderItems) e abater estoque
        foreach ($cart as $id => $quantity) {
            $product = $productsById[$id];
            
            // Criar o item
            $item = new OrderItem(null, $orderId, $id, $quantity, (float)$product['price']);
            $this->orderRepo->createItem($item);
            
            // Abater estoque
            $this->productRepo->decreaseStock($id, $quantity);
        }

        // 4. Limpar carrinho
        unset($_SESSION['cart']);

        Flash::push('success', 'Pedido #' . $orderId . ' realizado com sucesso! Obrigado pela sua compra.');
        return new RedirectResponse('/my-orders');
    }
}