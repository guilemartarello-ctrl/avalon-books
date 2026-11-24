<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$carrinho = $_SESSION['carrinho'] ?? [];
$itensDetalhados = [];
$totalGeral = 0;

if (!empty($carrinho)) {
    $stmt = $pdo->prepare('SELECT id, nome, preco, estoque, imagem FROM produtos WHERE id = ? AND ativo = 1');
    foreach ($carrinho as $produtoId => $qtd) {
        $stmt->execute([(int)$produtoId]);
        $prod = $stmt->fetch();
        if ($prod) {
            $subtotal = $prod['preco'] * $qtd;
            $totalGeral += $subtotal;
            $itensDetalhados[] = [
                'produto'  => $prod,
                'qtd'      => $qtd,
                'subtotal' => $subtotal,
            ];
        }
    }
}
?>

<h2 class="mb-4">Seu Carrinho de Compras</h2>

<?php if (empty($itensDetalhados)): ?>
    <div class="alert alert-info">
        Seu carrinho está vazio. <a href="index.php" class="alert-link">Explorar livros</a>.
    </div>
<?php else: ?>
    <div class="table-responsive bg-white rounded shadow-sm p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço Unitário</th>
                    <th style="width: 180px;">Quantidade</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itensDetalhados as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['produto']['nome']) ?></td>
                        <td>R$ <?= number_format($item['produto']['preco'], 2, ',', '.') ?></td>
                        <td>
                            <form action="actions/carrinho_acoes.php" method="POST" class="d-flex gap-1">
                                <input type="hidden" name="acao" value="atualizar">
                                <input type="hidden" name="produto_id" value="<?= $item['produto']['id'] ?>">
                                <input type="number" name="quantidade" class="form-control form-control-sm" value="<?= $item['qtd'] ?>" min="1" max="<?= $item['produto']['estoque'] ?>">
                                <button type="submit" class="btn btn-outline-primary btn-sm">OK</button>
                            </form>
                            <small class="text-muted">Máx: <?= $item['produto']['estoque'] ?></small>
                        </td>
                        <td class="fw-bold">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                        <td>
                            <form action="actions/carrinho_acoes.php" method="POST">
                                <input type="hidden" name="acao" value="remover">
                                <input type="hidden" name="produto_id" value="<?= $item['produto']['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Remover</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 bg-white p-3 rounded shadow-sm">
        <a href="index.php" class="btn btn-outline-secondary">Continuar comprando</a>
        <div class="text-end">
            <h4 class="mb-2">Total Geral: <span class="text-success">R$ <?= number_format($totalGeral, 2, ',', '.') ?></span></h4>
            <a href="checkout.php" class="btn btn-success btn-lg">Finalizar Compra</a>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>