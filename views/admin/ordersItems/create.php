<?php $this->layout('layouts/admin', ['title' => 'Adicionar Item ao Pedido']) ?>

<?php 
$preSelectedOrderId = $_GET['order_id'] ?? ($old['order_id'] ?? '');
?>

<?php $this->start('body') ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm" id="formView">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">Adicionar Produto ao Pedido</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/admin/order-items/store">
                        
                        <div class="mb-4">
                            <label for="order_id" class="form-label fw-bold">Pedido Destino</label>
                            <select class="form-select form-select-lg" id="order_id" name="order_id" required>
                                <option value="">Selecione o pedido...</option>
                                <?php foreach ($orders as $order): ?>
                                    <option value="<?= $this->e($order['order_id']) ?>" 
                                        <?= ($preSelectedOrderId == $order['order_id']) ? 'selected' : '' ?>>
                                        Pedido #<?= $this->e($order['order_id']) ?> 
                                        (Usuário ID: <?= $this->e($order['user_id']) ?>) 
                                        - <?= date('d/m/Y', strtotime($order['order_date'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['order_id'])): ?>
                                <div class="text-danger small mt-1"><?= $this->e($errors['order_id']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">O produto será vinculado a este pedido.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="product_id" class="form-label">Produto</label>
                                <select class="form-select" id="product_id" name="product_id" required>
                                    <option value="">Escolha um produto...</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= $this->e($product['id']) ?>" 
                                            data-price="<?= $this->e($product['price']) ?>"
                                            <?= (($old['product_id'] ?? '') == $product['id']) ? 'selected' : '' ?>>
                                            <?= $this->e($product['name']) ?> 
                                            (R$ <?= number_format($product['price'], 2, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['product_id'])): ?>
                                    <div class="text-danger small"><?= $this->e($errors['product_id']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Quantidade</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-123"></i></span>
                                    <input type="number" class="form-control" id="quantity" name="quantity"
                                           placeholder="1" value="<?= $this->e(($old['quantity'] ?? '1')) ?>" min="1" required>
                                </div>
                                <?php if (!empty($errors['quantity'])): ?>
                                    <div class="text-danger small"><?= $this->e($errors['quantity']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3" style="display:none;">
                                <label for="unit_price" class="form-label">Preço Unitário (R$)</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price"
                                           placeholder="0.00" value="<?= $this->e(($old['unit_price'] ?? '')) ?>">
                                </div>
                                <div class="form-text small">Preenchido automaticamente ao selecionar o produto.</div>
                                <?php if (!empty($errors['unit_price'])): ?>
                                    <div class="text-danger small"><?= $this->e($errors['unit_price']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="javascript:history.back()" class="btn btn-light border">Cancelar</a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-lg"></i> Salvar Item
                            </button>
                        </div>
                        <?= \App\Core\Csrf::input() ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productIdSelect = document.getElementById('product_id');
    const unitPriceInput = document.getElementById('unit_price');

    function updatePrice() {
        const selectedOption = productIdSelect.options[productIdSelect.selectedIndex];
        const price = selectedOption ? selectedOption.getAttribute('data-price') : '';
        if (price) {
            unitPriceInput.value = price;
        } else {
            unitPriceInput.value = '';
        }
    }

    productIdSelect.addEventListener('change', updatePrice);
    
    // Força o preenchimento se houver um produto pré-selecionado (útil após erro de validação)
    updatePrice();
});
</script>

<?php $this->stop() ?>