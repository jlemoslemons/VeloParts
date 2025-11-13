<?php $this->layout('layouts/admin', ['title' => 'Detalhe do Item de Pedido']) ?>

<?php $this->start('body') ?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalhes do Item de Pedido</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label"><strong>ID do Item:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($orderItem['item_id']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>ID do Pedido:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($orderItem['order_id']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>ID do Produto:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($orderItem['product_id']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Quantidade:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($orderItem['quantity']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Preço Unitário:</strong></label>
                    <input type="text" class="form-control" value="R$ <?= number_format($orderItem['unit_price'], 2, ',', '.') ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Subtotal:</strong></label>
                    <input type="text" class="form-control" value="R$ <?= number_format($orderItem['quantity'] * $orderItem['unit_price'], 2, ',', '.') ?>" readonly>
                </div>
                <div class="text-end">
                    <a href="javascript:history.back()" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop() ?>