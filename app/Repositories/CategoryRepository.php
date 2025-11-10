<?php
namespace App\Repositories;

use App\Core\Database;
use App\Models\Category;
use PDO;

class CategoryRepository
{
    // ... (countAll, paginate, find - idênticos) ...

    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM categories");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("SELECT * FROM categories ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(Category $category): int
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$category->name]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(Category $category): bool
    {
        $stmt = Database::getConnection()->prepare("UPDATE categories SET name = ? WHERE id = ?");
        return $stmt->execute([$category->name, $category->id]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getArray(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM categories ORDER BY id DESC");
        $stmt->execute();
        $categories = $stmt->fetchAll();
        $return = [];
        foreach ($categories as $category) {
            $return[$category['id']] = $category['name'];
        }
        return $return;
    }
}