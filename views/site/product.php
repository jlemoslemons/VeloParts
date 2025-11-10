<?php $this->layout('layouts/site', ['title' => $product['name']]) ?>

<?php $this->start('body') ?>

<div class="row">
    <div class="col-md-6">
        <img src="<?= $this->e($product['image_path'] ?? '/img/placeholder.png') ?>" class="img-fluid rounded shadow-sm" alt="<?= $this->e($product['name']) ?>">
    </div>
    <div class="col-md-6">
        <h1><?= $this->e($product['name']) ?></h1>
        
        <p class="fs-2 fw-bold text-success">
             R$ <?= number_format((float)$product['price'], 2, ',', '.') ?>
        </p>

        <p class="text-muted">
            <?php if ($product['stock'] > 0): ?>
                <span class="badge bg-success fs-6">Em Estoque (<?= $product['stock'] ?> unidades)</span>
            <?php else: ?>
                <span class="badge bg-danger fs-6">Indisponível</span>
            <?php endif; ?>
        </p>

        <hr>

        <h4>Descrição</h4>
        <p><?= nl2br($this->e($product['description'] ?? 'Sem descrição.')) ?></p>

        <hr>

        <?php if ($product['stock'] > 0): ?>
            <form action="/cart/add" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="row">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantidade</label>
                        <input type="number" class="form-control" name="quantity" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg mt-3">
                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                </button>
            </form>
        <?php else: ?>
             <button class="btn btn-secondary btn-lg mt-3" disabled>Produto Indisponível</button>
        <?php endif; ?>

    </div>
</div>


<?php $this->stop() ?>