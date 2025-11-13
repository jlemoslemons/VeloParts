<?php $this->layout('layouts/admin', ['title' => 'Detalhe do Pedido']) ?>

<?php $this->start('body') ?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalhes do Pedido</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label"><strong>ID do Pedido:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($order['order_id']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>ID do Usuário:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($order['user_id']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Data do Pedido:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($order['order_date']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Status:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($order['status']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Valor Total:</strong></label>
                    <input type="text" class="form-control" value="R$ <?= number_format($order['total_amount'], 2, ',', '.') ?>" readonly>
                </div>
                <div class="text-end">
                    <a href="javascript:history.back()" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop() ?>