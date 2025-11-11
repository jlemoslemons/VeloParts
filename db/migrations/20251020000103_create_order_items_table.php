<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrderItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('order_items')
            ->addColumn('order_id', 'integer')
            ->addColumn('product_id', 'integer')
            ->addColumn('quantity', 'integer')
            ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2]) // Preço no momento da compra
            ->addForeignKey('order_id', 'orders', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('product_id', 'products', 'id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->create();
    }
}