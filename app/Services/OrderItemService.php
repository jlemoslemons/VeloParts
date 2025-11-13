<?php

namespace App\Services;

use App\Models\OrderItem;

class OrderItemService
{
    public function validate(array $data): array
    {
        $errors = [];
        $order_id = $data['order_id'] ?? '';
        $product_id = $data['product_id'] ?? '';
        $quantity = $data['quantity'] ?? '';
        $unit_price = $data['unit_price'] ?? '';

        if ($order_id === '') $errors['order_id'] = 'Pedido é obrigatório';
        if ($product_id === '') $errors['product_id'] = 'Produto é obrigatório';
        if (!is_numeric($quantity) || (int)$quantity <= 0) {
            $errors['quantity'] = 'Quantidade deve ser numérica e maior que zero';
        }
        if (!is_numeric($unit_price) || (float)$unit_price <= 0) {
            $errors['unit_price'] = 'Preço unitário deve ser numérico e maior que zero';
        }

        return $errors;
    }

    public function make(array $data): OrderItem
    {
        $order_id = (int)($data['order_id'] ?? 0);
        $product_id = (int)($data['product_id'] ?? 0);
        $quantity = (int)($data['quantity'] ?? 0);
        $unit_price = (float)($data['unit_price'] ?? 0);
        $item_id = isset($data['item_id']) ? (int)$data['item_id'] : null;
        
        return new OrderItem($item_id, $order_id, $product_id, $quantity, $unit_price);
    }
}