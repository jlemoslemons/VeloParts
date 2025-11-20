<?php $this->layout('layouts/admin', ['title' => 'Pedidos']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="tableView">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-semibold">Gerenciamento de Pedidos</h5>
        <a href="/admin/orders/create" class="btn btn-primary" id="btnNewOrder">
            <i class="bi bi-plus-lg"></i> Novo Pedido
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    <th>ID Usuário</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="fw-bold">#<?= $this->e($order['order_id']) ?></td>
                        <td>
                            <i class="bi bi-person"></i> <?= $this->e($order['user_id']) ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                        <td>
                            <?php
                            $statusClass = match($order['status']) {
                                'completed' => 'bg-success',
                                'processing' => 'bg-primary',
                                'cancelled' => 'bg-danger',
                                'pending' => 'bg-warning text-dark',
                                default => 'bg-secondary'
                            };
                            $statusLabel = match($order['status']) {
                                'completed' => 'Concluído',
                                'processing' => 'Processando',
                                'cancelled' => 'Cancelado',
                                'pending' => 'Pendente',
                                default => $order['status']
                            };
                            ?>
                            <span class="badge rounded-pill <?= $statusClass ?>"><?= $this->e($statusLabel) ?></span>
                        </td>
                        <td class="fw-bold text-success">R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="/admin/orders/show?id=<?= $this->e($order['order_id']) ?>" title="Ver Detalhes e Itens">
                                    <i class="bi bi-eye-fill"></i> Detalhes
                                </a>
                                <form class="d-inline" action="/admin/orders/delete" method="post"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este pedido permanentemente?');">
                                    <input type="hidden" name="id" value="<?= $this->e($order['order_id']) ?>">
                                    <?= \App\Core\Csrf::input() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($orders)): ?>
            <div class="text-center p-4 text-muted">Nenhum pedido encontrado.</div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="/admin/orders?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php $this->stop() ?>