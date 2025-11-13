<?php $this->layout('layouts/admin', ['title' => 'Itens de Pedido']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="tableView">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-semibold">Lista de Itens de Pedido</h5>
        <a href="/admin/order-items/create" class="btn btn-primary" id="btnNewOrderItem">
            <i class="bi bi-plus-lg"></i> Novo Item de Pedido
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>ID Item</th>
                    <th>ID Pedido</th>
                    <th>ID Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?= $this->e($item['item_id']) ?></td>
                        <td><?= $this->e($item['order_id']) ?></td>
                        <td><?= $this->e($item['product_id']) ?></td>
                        <td><?= $this->e($item['quantity']) ?></td>
                        <td>R$ <?= number_format($item['unit_price'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-secondary btn-edit"
                                   href="/admin/order-items/show?id=<?= $this->e($item['item_id']) ?>">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <form class="inline" action="/admin/order-items/delete" method="post"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este item? (ID: <?= $this->e($item['item_id']) ?>)');">
                                    <input type="hidden" name="id" value="<?= $this->e($item['item_id']) ?>">
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
            <a href="/admin/order-items?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>

<?php $this->stop() ?>