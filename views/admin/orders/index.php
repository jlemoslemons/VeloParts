<?php $this->layout('layouts/admin', ['title' => 'Pedidos']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="tableView">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-semibold">Lista de Pedidos</h5>
        <a href="/admin/orders/create" class="btn btn-primary" id="btnNewOrder">
            <i class="bi bi-plus-lg"></i> Novo Pedido
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Usuário ID</th>
                    <th>Data do Pedido</th>
                    <th>Status</th>
                    <th>Valor Total</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $this->e($order['order_id']) ?></td>
                        <td><?= $this->e($order['user_id']) ?></td>
                        <td><?= $this->e($order['order_date']) ?></td>
                        <td>
                            <?php
                            $statusClass = match($order['status']) {
                                'completed' => 'badge bg-success',
                                'processing' => 'badge bg-primary',
                                'cancelled' => 'badge bg-danger',
                                'pending' => 'badge bg-warning',
                                default => 'badge bg-secondary'
                            };
                            ?>
                            <span class="<?= $statusClass ?>"><?= $this->e($order['status']) ?></span>
                        </td>
                        <td>R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-secondary btn-edit"
                                   href="/admin/orders/show?id=<?= $this->e($order['order_id']) ?>">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <form class="inline" action="/admin/orders/delete" method="post"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este pedido? (ID: <?= $this->e($order['order_id']) ?>)');">
                                    <input type="hidden" name="id" value="<?= $this->e($order['order_id']) ?>">
                                    <?= \App\Core\Csrf::input() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pagination" style="margin-top:12px;">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong>[<?= $i ?>]</strong>
        <?php else: ?>
            <a href="/admin/orders?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>

<?php $this->stop() ?>