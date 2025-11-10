<?php $this->layout('layouts/site', ['title' => 'Categoria: ' . $category['name']]) ?>

<?php $this->start('body') ?>

<h1>Categoria: <?= $this->e($category['name']) ?></h1>
<hr>
<div class="row row-cols-1 row-cols-md-4 g-4">
    <?php if (empty($products)): ?>
        <p>Nenhum produto encontrado nesta categoria.</p>
    <?php endif; ?>
    
    <?php foreach ($products as $product): ?>
    <div class="col">
        <div class="card h-100">
            <a href="/product?id=<?= $product['id'] ?>">
                <img src="<?= $this->e($product['image_path'] ?? '/img/placeholder.png') ?>" class="card-img-top" alt="<?= $this->e($product['name']) ?>" style="height: 200px; object-fit: cover;">
            </a>
            <div class="card-body">
                <h5 class="card-title"><?= $this->e($product['name']) ?></h5>
                <p class="card-text fw-bold fs-5 text-success">
                    R$ <?= number_format((float)$product['price'], 2, ',', '.') ?>
                </p>
                 <?php if ($product['stock'] <= 0): ?>
                     <span class="badge bg-danger">Indisponível</span>
                <?php else: ?>
                     <span class="badge bg-info">Estoque: <?= $product['stock'] ?></span>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                 <a href="/product?id=<?= $product['id'] ?>" class="btn btn-primary w-100">Ver Detalhes</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php $this->stop() ?>  