<?php $this->layout('layouts/site', ['title' => 'Carrinho de Compras']) ?>

<?php $this->start('body') ?>

<h1>Carrinho de Compras</h1>
<hr>

<?php if (empty($products)): ?>
    <div class="alert alert-info">Seu carrinho está vazio.</div>
    <a href="/" class="btn btn-primary">Ver produtos</a>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" colspan="2">Produto</th>
                    <th scope="col">Preço</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <img src="<?= $this->e($p['image_path'] ?? '/img/placeholder.png') ?>" alt="<?= $this->e($p['name']) ?>" style="width: 80px; height: 80px; object-fit: cover;">
                    </td>
                    <td>
                        <a href="/product?id=<?= $p['id'] ?>" class="text-dark text-decoration-none"><?= $this->e($p['name']) ?></a>
                        <br>
                        <small class="text-muted">Estoque: <?= $p['stock'] ?></small>
                    </td>
                    <td>R$ <?= number_format($p['price'], 2, ',', '.') ?></td>
                    <td>
                        <form action="/cart/update" method="POST" class="d-flex">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <input type="number" class="form-control form-control-sm" name="quantity" value="<?= $p['quantity'] ?>" min="1" max="<?= $p['stock'] ?>" style="width: 80px;">
                            <button type="submit" class="btn btn-outline-secondary btn-sm ms-2" title="Atualizar">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </form>
                    </td>
                    <td>R$ <?= number_format($p['subtotal'], 2, ',', '.') ?></td>
                    <td>
                        <form action="/cart/remove" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" title="Remover">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="row mt-4 justify-content-end">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total do Pedido</h5>
                    <p class="card-text fs-3 fw-bold">
                        R$ <?= number_format($total, 2, ',', '.') ?>
                    </p>
                    <a href="/checkout" class="btn btn-success btn-lg w-100">
                        Ir para o Checkout <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $this->stop() ?>