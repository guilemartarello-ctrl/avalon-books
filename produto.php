<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, c.nome AS categoria_nome FROM produtos p INNER JOIN categorias c ON c.id = p.categoria_id WHERE p.id = ? AND p.ativo = 1');
$stmt->execute([$id]);
$livro = $stmt->fetch();

if (!$livro): ?>
    <div class="alert alert-danger">Produto não encontrado. <a href="index.php">Voltar para a vitrine</a>.</div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="<?= htmlspecialchars($livro['imagem']) ?>" class="img-fluid rounded-start w-100" alt="<?= htmlspecialchars($livro['nome']) ?>">
            </div>
            <div class="col-md-8">
                <div class="card-body p-4">
                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($livro['categoria_nome']) ?></span>
                    <h2 class="card-title"><?= htmlspecialchars($livro['nome']) ?></h2>
                    <p class="text-muted small">Código do produto: #<?= $livro['id'] ?></p>
                    <h3 class="text-success fw-bold my-3">R$ <?= number_format($livro['preco'], 2, ',', '.') ?></h3>
                    <p class="card-text"><?= nl2br(htmlspecialchars($livro['descricao'])) ?></p>

                    <p class="fw-semibold">
                        Disponibilidade:
                        <?php if ($livro['estoque'] > 0): ?>
                            <span class="text-success"><?= $livro['estoque'] ?> unidades disponíveis</span>
                        <?php else: ?>
                            <span class="text-danger">Produto esgotado</span>
                        <?php endif; ?>
                    </p>

                    <?php if ($livro['estoque'] > 0): ?>
                        <form action="actions/carrinho_acoes.php" method="POST" class="row g-2 align-items-center mt-3" style="max-width: 320px;">
                            <input type="hidden" name="acao" value="adicionar">
                            <input type="hidden" name="produto_id" value="<?= $livro['id'] ?>">
                            <div class="col-5">
                                <input type="number" name="quantidade" class="form-control" value="1" min="1" max="<?= $livro['estoque'] ?>">
                            </div>
                            <div class="col-7 d-grid">
                                <button type="submit" class="btn btn-primary">Comprar agora</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-outline-secondary btn-sm">&larr; Voltar à vitrine</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>