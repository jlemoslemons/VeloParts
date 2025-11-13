<?php $this->layout('layouts/admin', ['title' => 'Novo Item de Pedido']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="formView">
    <?php $this->insert('partials/admin/form/header', ['title' => 'Novo item de pedido']) ?>
    <div class="card-body">
        <form method="post" action="/admin/order-items/store" enctype="multipart/form-data" class="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="order_id" class="form-label">Pedido</label>
                    <select class="form-control" id="order_id" name="order_id" required>
                        <option value="">Selecione um pedido</option>
                        <?php foreach ($orders as $order): ?>
                            <option value="<?= $this->e($order['order_id']) ?>" 
                                <?= (($old['order_id'] ?? '') == $order['order_id']) ? 'selected' : '' ?>>
                                Pedido #<?= $this->e($order['order_id']) ?> - Usuário: <?= $this->e($order['user_id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['order_id'])): ?>
                        <div class="text-danger"><?= $this->e($errors['order_id']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="product_id" class="form-label">Produto</label>
                    <select class="form-control" id="product_id" name="product_id" required>
                        <option value="">Selecione um produto</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $this->e($product['id']) ?>" 
                                <?= (($old['product_id'] ?? '') == $product['id']) ? 'selected' : '' ?>>
                                <?= $this->e($product['name']) ?> - R$ <?= number_format($product['price'], 2, ',', '.') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['product_id'])): ?>
                        <div class="text-danger"><?= $this->e($errors['product_id']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="quantity" class="form-label">Quantidade</label>
                    <input type="number" class="form-control" id="quantity" name="quantity"
                           placeholder="Digite a quantidade" value="<?= $this->e(($old['quantity'] ?? '')) ?>" min="1" required>
                    <?php if (!empty($errors['quantity'])): ?>
                        <div class="text-danger"><?= $this->e($errors['quantity']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="unit_price" class="form-label">Preço Unitário</label>
                    <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price"
                           placeholder="0.00" value="<?= $this->e(($old['unit_price'] ?? '')) ?>" required>
                    <?php if (!empty($errors['unit_price'])): ?>
                        <div class="text-danger"><?= $this->e($errors['unit_price']) ?></div>
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
                <a href="/admin/order-items" class="btn align-self-end">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
            <?= \App\Core\Csrf::input() ?>
        </form>
    </div>
</div>
<?php $this->stop() ?>