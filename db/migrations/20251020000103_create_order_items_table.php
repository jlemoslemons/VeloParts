<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrderItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('order_items', ['id' => 'item_id'])
            ->addColumn('order_id', 'integer', ['signed' => false])
            ->addColumn('product_id', 'integer', ['signed' => false])
            ->addColumn('quantity', 'integer')
            ->addColumn('unit_price', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addForeignKey('order_id', 'orders', 'order_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('product_id', 'products', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}