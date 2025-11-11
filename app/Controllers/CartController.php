<?php
namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Repositories\ProductRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController
{
    private View $view;
    private ProductRepository $productRepo;

    public function __construct()
    {
        Csrf::ensureSession(); // Garante que a sessão está ativa
        $this->view = new View();
        $this->productRepo = new ProductRepository();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Adicionar item ao carrinho
    public function add(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }
        
        $id = (int)$request->request->get('id', 0);
        $qty = (int)$request->request->get('quantity', 1);
        if ($qty <= 0) $qty = 1;

        $product = $this->productRepo->find($id);
        if (!$product || $id <= 0) {
            Flash::push('danger', 'Produto não encontrado.');
            return new RedirectResponse('/cart');
        }

        // Verifica estoque
        $currentInCart = $_SESSION['cart'][$id] ?? 0;
        if (($qty + $currentInCart) > $product['stock']) {
            Flash::push('warning', 'Estoque insuficiente. Máximo: ' . $product['stock']);
            return new RedirectResponse('/product?id=' . $id);
        }
        
        // Adiciona ou soma a quantidade
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }

        Flash::push('success', 'Produto adicionado ao carrinho!');
        return new RedirectResponse('/cart');
    }

    // Exibir o carrinho
    public function show(): Response
    {
        $cart = $_SESSION['cart'];
        $products = [];
        $total = 0.0;

        if (!empty($cart)) {
            $productIds = array_keys($cart);
            $dbProducts = $this->productRepo->findIn($productIds);
            
            foreach($dbProducts as $p) {
                $quantity = $cart[$p['id']];
                $subtotal = $p['price'] * $quantity;
                $total += $subtotal;
                
                $products[] = [
                    'id' => $p['id'],
                    'name' => $p['name'],
                    'price' => $p['price'],
                    'image_path' => $p['image_path'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'stock' => $p['stock']
                ];
            }
        }
        
        $html = $this->view->render('site/cart', compact('products', 'total'));
        return new Response($html);
    }

    // Atualizar quantidade
    public function update(Request $request): Response
    {
        $id = (int)$request->request->get('id', 0);
        $qty = (int)$request->request->get('quantity', 1);

        if ($qty <= 0) {
             unset($_SESSION['cart'][$id]); // Remove se for 0 ou menos
             Flash::push('info', 'Item removido.');
             return new RedirectResponse('/cart');
        }

        $product = $this->productRepo->find($id);
        if (!$product || !isset($_SESSION['cart'][$id])) {
             return new RedirectResponse('/cart');
        }

        // Verifica estoque
        if ($qty > $product['stock']) {
            $_SESSION['cart'][$id] = $product['stock']; // Limita ao estoque
            Flash::push('warning', 'Estoque atualizado para o máximo disponível: ' . $product['stock']);
        } else {
            $_SESSION['cart'][$id] = $qty;
            Flash::push('success', 'Quantidade atualizada.');
        }

        return new RedirectResponse('/cart');
    }

    // Remover item
    public function remove(Request $request): Response
    {
        $id = (int)$request->request->get('id', 0);
        unset($_SESSION['cart'][$id]);
        Flash::push('info', 'Item removido do carrinho.');
        return new RedirectResponse('/cart');
    }
}