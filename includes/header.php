<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$totalItensCarrinho = 0;
if (!empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $qtd) {
        $totalItensCarrinho += $qtd;
    }
}

// Ajusta o caminho base caso a página esteja dentro da pasta /admin
$base = str_contains($_SERVER['SCRIPT_NAME'], '/admin/') ? '../' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avalon Books - Livraria Virtual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $base ?>index.php">Avalon Books</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>index.php">Vitrine</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>carrinho.php">
                        Carrinho <span class="badge bg-primary"><?= $totalItensCarrinho ?></span>
                    </a>
                </li>
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['perfil'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-semibold" href="<?= $base ?>admin/index.php">Painel Admin</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <span class="text-light small me-2">Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></span>
                    <a href="<?= $base ?>actions/auth_logout.php" class="btn btn-outline-light btn-sm">Sair</a>
                <?php else: ?>
                    <a href="<?= $base ?>login.php" class="btn btn-outline-light btn-sm">Entrar</a>
                    <a href="<?= $base ?>cadastro.php" class="btn btn-primary btn-sm">Cadastrar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="container flex-grow-1 mb-5">
<?php if (!empty($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] ?> alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['mensagem']['texto']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
<?php endif; ?>