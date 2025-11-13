<?php

namespace App\Models;

class Order
{
    public ?int $order_id;
    public int $user_id;
    public string $order_date;
    public string $status;
    public float $total_amount;

    public function __construct(?int $order_id, int $user_id, string $order_date, string $status, float $total_amount)
    {
        $this->order_id = $order_id;
        $this->user_id = $user_id;
        $this->order_date = $order_date;
        $this->status = $status;
        $this->total_amount = $total_amount;
    }
}