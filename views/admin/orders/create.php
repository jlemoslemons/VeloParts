<?php $this->layout('layouts/admin', ['title' => 'Novo Pedido']) ?>

<?php $this->start('body') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Criar Novo Pedido</h6>
    </div>
    <div class="card-body">
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $this->e($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form action="/admin/orders/store" method="post">
            <?= \App\Core\Csrf::input() ?>

            <div class="mb-3">
                <label for="user_id" class="form-label">Cliente</label>
                <select name="user_id" id="user_id" class="form-select" required>
                    <option value="">Selecione um cliente...</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= (isset($old['user_id']) && $old['user_id'] == $user['id']) ? 'selected' : '' ?>>
                            <?= $this->e($user['name']) ?> (<?= $this->e($user['email']) ?>)
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="order_date" class="form-label">Data e Hora</label>
                <input type="datetime-local" class="form-control" id="order_date" name="order_date" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status Inicial</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="pending" selected>Pendente</option>
                    <option value="processing">Em Processamento</option>
                    <option value="completed">Concluído</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>

            <div class="d-flex justify-content-end">
                <a href="/admin/orders" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Criar e Adicionar Itens
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('order_date');
        if (dateInput) {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dateInput.value = now.toISOString().slice(0, 16);
        }
    });
</script>
<?php $this->stop() ?>