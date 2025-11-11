<?php
// $allCategories é injetado pelo App\Core\View
$auth = \App\Services\AuthService::user();
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    $cartCount = count($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale1.0">
    <title><?= $this->e($title ?? 'AutoParts E-commerce') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">AutoParts</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        Categorias
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach ($allCategories as $cat): ?>
                            <li><a class="dropdown-item" href="/category?id=<?= $cat['id'] ?>"><?= $this->e($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/cart">
                        <i class="bi bi-cart"></i> Carrinho
                        <?php if ($cartCount > 0): ?>
                            <span class="badge bg-success"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if ($auth): ?>
                    <li class="nav-item dropdown">
                         <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= $this->e(explode(' ', $auth['name'])[0]) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/my-orders">Meus Pedidos</a></li>
                            <?php if ($auth['email'] === 'teste@teste.com'): // Exemplo de admin ?>
                                <li><a class="dropdown-item" href="/admin">Painel Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="/auth/logout" method="POST" class="d-inline">
                                    <?= \App\Core\Csrf::input() ?>
                                    <button type="submit" class="dropdown-item">Sair</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php $flashes = \App\Core\Flash::pullAll(); if ($flashes): ?>
        <?php foreach ($flashes as $f): ?>
            <div class="alert alert-<?= $this->e($f['type']) ?> alert-dismissible fade show" role="alert">
                <?= $this->e($f['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?= $this->section('body') ?>
</div>

<footer class="text-center text-muted py-4 mt-5 bg-light">
    <p>&copy; 2025 AutoParts E-commerce</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>