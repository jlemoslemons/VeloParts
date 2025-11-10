<?php $this->layout('layouts/site', ['title' => 'Finalizar Pedido']) ?>

<?php $this->start('body') ?>
<?php $user = \App\Services\AuthService::user(); ?>

<h1>Finalizar Pedido</h1>
<hr>

<div class="row">
    <div class="col-md-8">
        <h4>Resumo dos Itens</h4>
        <ul class="list-group">
            <?php foreach ($products as $p): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <?= $this->e($p['name']) ?>
                    <br>
                    <small class="text-muted">Qtd: <?= $cart[$p['id']] ?> x R$ <?= number_format($p['price'], 2, ',', '.') ?></small>
                </div>
                <span class="fw-bold">R$ <?= number_format($p['price'] * $cart[$p['id']], 2, ',', '.') ?></span>
            </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                <h5 class="mb-0">Total</h5>
                <h5 class="mb-0 fw-bold text-success">R$ <?= number_format($total, 2, ',', '.') ?></h5>
            </li>
        </ul>
    </div>
    
    <div class="col-md-4">
        <h4>Informações do Cliente</h4>
        <div class="card">
            <div class="card-body">
                <p><strong>Nome:</strong> <?= $this->e($user['name']) ?></p>
                <p><strong>Email:</strong> <?= $this->e($user['email']) ?></p>
                <hr>
                <p class="text-muted small">O pedido será registrado em sua conta. A implementação do pagamento (ex: Stripe, PagSeguro) ocorreria nesta etapa.</p>
                
                <form action="/checkout/place" method="POST">
                    <?= \App\Core\Csrf::input() ?>
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-check-circle"></i> Confirmar e Finalizar Pedido
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->stop() ?>