<?php $this->layout('layouts/admin', ['title' => 'Novo Pedido']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="formView">
    <?php $this->insert('partials/admin/form/header', ['title' => 'Novo pedido']) ?>
    <div class="card-body">
        <form method="post" action="/admin/orders/store" enctype="multipart/form-data" class="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_id" class="form-label">Usuário</label>
                    <select class="form-control" id="user_id" name="user_id" required>
                        <option value="">Selecione um usuário</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $this->e($user['id']) ?>" 
                                <?= (($old['user_id'] ?? '') == $user['id']) ? 'selected' : '' ?>>
                                <?= $this->e($user['name']) ?> - <?= $this->e($user['email']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['user_id'])): ?>
                        <div class="text-danger"><?= $this->e($errors['user_id']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="order_date" class="form-label">Data do Pedido</label>
                    <input type="datetime-local" class="form-control" id="order_date" name="order_date"
                           value="<?= $this->e(($old['order_date'] ?? '')) ?>" required>
                    <?php if (!empty($errors['order_date'])): ?>
                        <div class="text-danger"><?= $this->e($errors['order_date']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="">Selecione um status</option>
                        <option value="pending" <?= (($old['status'] ?? '') == 'pending') ? 'selected' : '' ?>>Pendente</option>
                        <option value="processing" <?= (($old['status'] ?? '') == 'processing') ? 'selected' : '' ?>>Processando</option>
                        <option value="completed" <?= (($old['status'] ?? '') == 'completed') ? 'selected' : '' ?>>Completo</option>
                        <option value="cancelled" <?= (($old['status'] ?? '') == 'cancelled') ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                        <div class="text-danger"><?= $this->e($errors['status']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="total_amount" class="form-label">Valor Total</label>
                    <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount"
                           placeholder="0.00" value="<?= $this->e(($old['total_amount'] ?? '')) ?>" required>
                    <?php if (!empty($errors['total_amount'])): ?>
                        <div class="text-danger"><?= $this->e($errors['total_amount']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> Salvar
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Limpar
                </button>
                <a href="/admin/orders" class="btn align-self-end">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
            <?= \App\Core\Csrf::input() ?>
        </form>
    </div>
</div>
<?php $this->stop() ?>