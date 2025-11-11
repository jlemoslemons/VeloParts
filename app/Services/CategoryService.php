<?php
namespace App\Services;

use App\Models\Category;

class CategoryService {
    public function validate(array $data): array {
        $errors = [];
        $name = trim($data['name'] ?? '');
    
        if ($name === '') $errors['name'] = 'Nome é obrigatório';
        if (strlen($name) > 100) $errors['name'] = 'Nome muito longo (máx 100).';

        return $errors;
    }

    public function make(array $data): Category {
        $name = trim($data['name'] ?? '');
        $id = isset($data['id']) ? (int)$data['id'] : null;
        return new Category($id, $name);
    }
}