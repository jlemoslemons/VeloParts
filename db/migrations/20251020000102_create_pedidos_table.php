<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePedidosTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('pedidos', ['id' => 'id_pedido'])
            ->addColumn('id_usuario', 'integer', ['signed' => false])
            ->addColumn('data_pedido', 'datetime')
            ->addColumn('status', 'string', ['limit' => 20])
            ->addColumn('valor_total', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addForeignKey('id_usuario', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}