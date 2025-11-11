<?php
namespace App\Models;

class Product
{
    public ?int $id;
    public string $name;
    public ?string $description;
    public float $price;
    public int $stock;
    public int $category_id;
    public ?string $image_path;
    public string $created_at;

    public function __construct(
        ?int $id,
        string $name,
        ?string $description,
        float $price,
        int $stock,
        int $category_id,
        ?string $image_path = null,
        string $created_at = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->category_id = $category_id;
        $this->image_path = $image_path;
        $this->created_at = $created_at;
    }
}