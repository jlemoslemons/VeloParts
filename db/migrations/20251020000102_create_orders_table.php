<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrdersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('orders', ['id' => 'order_id'])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('order_date', 'datetime')
            ->addColumn('status', 'string', ['limit' => 20])
            ->addColumn('total_amount', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}