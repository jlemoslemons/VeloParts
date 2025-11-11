<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateItensPedidoTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('itens_pedido', ['id' => 'id_item'])
            ->addColumn('id_pedido', 'integer', ['signed' => false])
            ->addColumn('id_produto', 'integer', ['signed' => false])
            ->addColumn('quantidade', 'integer')
            ->addColumn('preco_unitario', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addForeignKey('id_pedido', 'pedidos', 'id_pedido', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('id_produto', 'products', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}