<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\OrderItem;
use PDO;

class OrderItemRepository
{
    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM order_items");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("SELECT * FROM order_items ORDER BY item_id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM order_items WHERE item_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(OrderItem $orderItem): int
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderItem->order_id, $orderItem->product_id, $orderItem->quantity, $orderItem->unit_price]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(OrderItem $orderItem): bool
    {
        $stmt = Database::getConnection()->prepare("UPDATE order_items SET order_id = ?, product_id = ?, quantity = ?, unit_price = ? WHERE item_id = ?");
        return $stmt->execute([$orderItem->order_id, $orderItem->product_id, $orderItem->quantity, $orderItem->unit_price, $orderItem->item_id]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM order_items WHERE item_id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM order_items ORDER BY item_id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByOrderId(int $orderId): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function findByProductId(int $productId): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM order_items WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
}