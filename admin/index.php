<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

// Controle de acesso no servidor
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'admin') {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Acesso restrito a administradores.'];
    header('Location: ../login.php');
    exit;
}

$produtos = $pdo->query('SELECT p.*, c.nome AS categoria_nome FROM produtos p INNER JOIN categorias c ON c.id = p.categoria_id ORDER BY p.id DESC')->fetchAll();

$pedidos = $pdo->query('SELECT ped.*, u.nome AS cliente_nome FROM pedidos ped INNER JOIN usuarios u ON u.id = ped.usuario_id ORDER BY ped.id DESC')->fetchAll();
$stmtItens = $pdo->prepare('SELECT ip.quantidade, ip.preco_unitario, pr.nome FROM itens_pedido ip INNER JOIN produtos pr ON pr.id = ip.produto_id WHERE ip.pedido_id = ?');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Painel Administrativo - Produtos</h2>
    <a href="produto_form.php" class="btn btn-success">+ Novo Produto</a>
</div>

<div class="table-responsive bg-white rounded shadow-sm p-3 mb-5">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Livro</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['categoria_nome']) ?></td>
                    <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                    <td><?= $p['estoque'] ?></td>
                    <td>
                        <?= $p['ativo'] ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>' ?>
                    </td>
                    <td class="d-flex gap-1">
                        <a href="produto_form.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <form action="produto_acoes.php" method="POST" onsubmit="return confirm('Alterar status ou excluir este produto?');">
                            <input type="hidden" name="acao" value="alternar_status">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <?= $p['ativo'] ? 'Desativar' : 'Ativar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3 class="mb-3">Pedidos Realizados</h3>
<div class="table-responsive bg-white rounded shadow-sm p-3">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Pagamento</th>
                <th>Itens Comprados</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pedidos)): ?>
                <tr><td colspan="6" class="text-muted">Nenhum pedido realizado ainda.</td></tr>
            <?php endif; ?>
            <?php foreach ($pedidos as $ped): ?>
                <?php
                $stmtItens->execute([$ped['id']]);
                $itens = $stmtItens->fetchAll();
                ?>
                <tr>
                    <td>#<?= $ped['id'] ?></td>
                    <td><?= htmlspecialchars($ped['cliente_nome']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($ped['criado_em'])) ?></td>
                    <td><?= htmlspecialchars($ped['forma_pagamento']) ?></td>
                    <td>
                        <ul class="mb-0 ps-3 small">
                            <?php foreach ($itens as $it): ?>
                                <li><?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?> (R$ <?= number_format($it['preco_unitario'], 2, ',', '.') ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td class="fw-bold text-success">R$ <?= number_format($ped['total'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>