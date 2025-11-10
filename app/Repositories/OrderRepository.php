<?php
namespace App\Repositories;

use App\Core\Database;
use App\Models\Order;
use App\Models\OrderItem;
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
        // Join com users para pegar o email do cliente
        $stmt = Database::getConnection()->prepare(
            "SELECT o.*, u.email as user_email 
             FROM orders o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.id DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT o.*, u.name as user_name, u.email as user_email
             FROM orders o 
             JOIN users u ON o.user_id = u.id
             WHERE o.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    
    public function findItemsByOrderId(int $order_id): array
    {
         $stmt = Database::getConnection()->prepare(
            "SELECT oi.*, p.name as product_name, p.image_path as product_image
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?"
        );
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();       
    }

    public function findByUserId(int $user_id): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function create(Order $order): int
    {
        $stmt = Database::getConnection()->prepare(
            "INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$order->user_id, $order->total_price, $order->status]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function createItem(OrderItem $item): int
    {
        $stmt = Database::getConnection()->prepare(
            "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$item->order_id, $item->product_id, $item->quantity, $item->price]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = Database::getConnection()->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}