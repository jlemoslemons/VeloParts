<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function validate(array $data): array
    {
        $errors = [];
        $user_id = $data['user_id'] ?? '';
        $order_date = trim($data['order_date'] ?? '');
        $status = trim($data['status'] ?? '');
        $total_amount = $data['total_amount'] ?? '';

        if ($user_id === '') $errors['user_id'] = 'Usuário é obrigatório';
        if ($order_date === '') $errors['order_date'] = 'Data do pedido é obrigatória';
        if ($status === '') $errors['status'] = 'Status é obrigatório';
        if (!is_numeric($total_amount) || (float)$total_amount < 0) {
            $errors['total_amount'] = 'Valor total deve ser numérico e maior ou igual a zero';
        }

        return $errors;
    }

    public function make(array $data): Order
    {
        $user_id = (int)($data['user_id'] ?? 0);
        $order_date = trim($data['order_date'] ?? '');
        $status = trim($data['status'] ?? '');
        $total_amount = (float)($data['total_amount'] ?? 0);
        $order_id = isset($data['order_id']) ? (int)$data['order_id'] : null;
        
        return new Order($order_id, $user_id, $order_date, $status, $total_amount);
    }
}