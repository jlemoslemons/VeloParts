<?php $this->layout('layouts/site', ['title' => 'Meus Pedidos']) ?>

<?php $this->start('body') ?>

<h1>Meus Pedidos</h1>
<hr>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">Você ainda não fez nenhum pedido.</div>
<?php else: ?>
    <div class="accordion" id="ordersAccordion">
        <?php foreach ($orders as $order): ?>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $order['id'] ?>">
                    <div class="d-flex justify-content-between w-100 me-3">
                        <span>Pedido #<?= $order['id'] ?></span>
                        <span>R$ <?= number_format($order['total_price'], 2, ',', '.') ?></span>
                        <span><?= $this->e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></span>
                        <span><span class="badge bg-primary"><?= $this->e($order['status']) ?></span></span>
                    </div>
                </button>
            </h2>
            <div id="collapse<?= $order['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                <div class="accordion-body">
                    <p>Itens deste pedido serão exibidos aqui.</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $this->stop() ?>