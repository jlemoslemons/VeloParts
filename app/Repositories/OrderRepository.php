<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Order;
use PDO;

class OrderRepository
{
    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM orders");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("SELECT * FROM orders ORDER BY order_id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(Order $order): int
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO orders (user_id, order_date, status, total_amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order->user_id, $order->order_date, $order->status, $order->total_amount]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(Order $order): bool
    {
        $stmt = Database::getConnection()->prepare("UPDATE orders SET user_id = ?, order_date = ?, status = ?, total_amount = ? WHERE order_id = ?");
        return $stmt->execute([$order->user_id, $order->order_date, $order->status, $order->total_amount, $order->order_id]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM orders WHERE order_id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM orders ORDER BY order_id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}