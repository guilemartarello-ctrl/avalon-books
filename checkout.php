<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensagem'] = ['tipo' => 'warning', 'texto' => 'Entre na sua conta ou cadastre-se para finalizar o pedido.'];
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

$total = 0;
$stmt = $pdo->prepare('SELECT nome, preco FROM produtos WHERE id = ?');
$resumo = [];

foreach ($_SESSION['carrinho'] as $produtoId => $qtd) {
    $stmt->execute([(int)$produtoId]);
    $prod = $stmt->fetch();
    if ($prod) {
        $sub = $prod['preco'] * $qtd;
        $total += $sub;
        $resumo[] = "{$qtd}x {$prod['nome']} (R$ " . number_format($sub, 2, ',', '.') . ")";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Checkout e Pagamento</h4>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></p>
                <ul class="list-group mb-3">
                    <?php foreach ($resumo as $linha): ?>
                        <li class="list-group-item"><?= htmlspecialchars($linha) ?></li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between fw-bold">
                        <span>Total a pagar</span>
                        <span class="text-success">R$ <?= number_format($total, 2, ',', '.') ?></span>
                    </li>
                </ul>

                <form action="actions/finalizar_compra.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Selecione a forma de pagamento:</label>
                        <select name="forma_pagamento" class="form-select" required>
                            <option value="PIX">PIX (Aprovação imediata)</option>
                            <option value="Cartao">Cartão de Crédito</option>
                            <option value="Boleto">Boleto Bancário</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">Confirmar Pagamento</button>
                        <a href="carrinho.php" class="btn btn-outline-secondary">Voltar ao carrinho</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>