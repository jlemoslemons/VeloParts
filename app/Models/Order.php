<?php
namespace App\Models;

class Order
{
    public ?int $id;
    public int $user_id;
    public float $total_price;
    public string $status;
    public string $created_at;

    public function __construct(
        ?int $id,
        int $user_id,
        float $total_price,
        string $status,
        string $created_at = ''
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->total_price = $total_price;
        $this->status = $status;
        $this->created_at = $created_at;
    }
}