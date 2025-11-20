<?php $this->start('body') ?>
<?php $this->layout('layouts/admin', ['title' => 'Detalhes do Pedido #' . $order['order_id']]) ?>

<div class="row">
    <!-- Coluna Principal (Esquerda) -->
    <div class="col-lg-8">

        <!-- Card de Informações -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dados do Pedido</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Cliente:</strong><br>
                        <?= $user ? $this->e($user['name']) : 'Desconhecido' ?>
                        <small class="text-muted">(<?= $user ? $this->e($user['email']) : '-' ?>)</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>Data:</strong><br>
                        <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Status Atual:</strong><br>
                        <?php
                        $badges = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $statusLabels = [
                            'pending' => 'Pendente',
                            'processing' => 'Em Processamento',
                            'completed' => 'Concluído',
                            'cancelled' => 'Cancelado'
                        ];
                        $badge = $badges[$order['status']] ?? 'secondary';
                        $label = $statusLabels[$order['status']] ?? $order['status'];
                        ?>
                        <span class="badge bg-<?= $badge ?> fs-6"><?= $label ?></span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>Total do Pedido:</strong><br>
                        <span class="h4 text-success">R$
                            <?= number_format($order['total_amount'], 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Itens -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Itens do Pedido</h6>
                <?php if (!in_array($order['status'], ['completed', 'cancelled'])): ?>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addItemModal">
                        <i class="bi bi-plus-circle"></i> Adicionar Item
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Unitário</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Nenhum item adicionado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $item):
                                    // BLINDAGEM: Verifica se a variável existe antes de usar
                                    $prod = isset($productsMap) ? ($productsMap[$item['product_id']] ?? null) : null;
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $prod ? $this->e($prod['name']) : 'Produto #' . $item['product_id'] ?>
                                        </td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end">R$ <?= number_format($item['unit_price'], 2, ',', '.') ?></td>
                                        <td class="text-end fw-bold">R$
                                            <?= number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') ?></td>
                                        <td class="text-center">
                                            <form action="/admin/order-items/delete" method="post" class="d-inline"
                                                onsubmit="return confirm('Remover este item?')">
                                                <?= \App\Core\Csrf::input() ?>
                                                <input type="hidden" name="id" value="<?= $item['item_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna Lateral (Direita) - AÇÕES -->
    <div class="col-lg-4">

        <!-- Card de Alteração de Status -->
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">Gerenciar Status</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">Alterar o status atualiza o fluxo do pedido.</p>

                <form action="/admin/orders/update-status" method="post">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="id" value="<?= $order['order_id'] ?>">

                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pendente
                            </option>
                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Em
                                Processamento</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Concluído
                            </option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelado
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-arrow-repeat"></i> Atualizar Status
                    </button>
                </form>
            </div>
        </div>

        <a href="/admin/orders" class="btn btn-secondary w-100">
            <i class="bi bi-arrow-left"></i> Voltar para Lista
        </a>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- CORREÇÃO DE ROTA: /order-items/store (singular) -->
                <form action="/admin/order-items/store" method="post">
                    <?= \App\Core\Csrf::input() ?>
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Produto</label>
                        <select name="product_id" class="form-select" required>
                            <?php
                            // Blindagem também aqui no modal
                            if (isset($productsMap)):
                                foreach ($productsMap as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= $this->e($p['name']) ?> - R$ <?= number_format($p['price'], 2, ',', '.') ?>
                                    </option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->stop() ?>