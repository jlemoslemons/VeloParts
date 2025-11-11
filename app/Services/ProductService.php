<?php
namespace App\Services;

use App\Models\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService {
    public function validate(array $data, ?UploadedFile $file = null): array {
        $errors = [];
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = $data['price'] ?? '';
        $stock = $data['stock'] ?? '';
        $category_id = $data['category_id'] ?? '';

        if ($name === '') $errors['name'] = 'Nome é obrigatório';
        if (!is_numeric($price) || (float)$price <= 0) $errors['price'] = 'Preço deve ser numérico e maior que zero';
        if (!is_numeric($stock) || (int)$stock < 0) $errors['stock'] = 'Estoque deve ser numérico (0 ou mais)';
        if ($category_id === '') $errors['category_id'] = 'Categoria é obrigatória';

        if ($file) {
            // ... (lógica de validação de imagem idêntica) ...
            $maxMb = (int)($_ENV['UPLOAD_MAX_MB'] ?? 5);
            $allowed = ['image/jpeg','image/png','image/webp'];
            if (!in_array($file->getMimeType(), $allowed, true)) {
                $errors['image'] = 'A imagem deve ser JPEG, PNG ou WEBP';
            }
            if ($file->getSize() > $maxMb * 1024 * 1024) {
                $errors['image'] = 'Tamanho máximo: ' . $maxMb . 'MB';
            }
        }

        return $errors;
    }

    // ... (storeImage - idêntico) ...
    public function storeImage(?UploadedFile $file): ?string {
        if (!$file) return null;
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = dirname(__DIR__, 2) . '/public/uploads/' . $name;
        $file->move(dirname($dest), $name);
        return '/uploads/' . $name;
    }

    public function make(array $data, ?string $imagePath = null): Product {
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = (float)($data['price'] ?? 0);
        $stock = (int)($data['stock'] ?? 0);
        $category_id = (int)($data['category_id'] ?? 0);
        $id = isset($data['id']) ? (int)$data['id'] : null;
        
        return new Product($id, $name, $description, $price, $stock, $category_id, $imagePath);
    }
}