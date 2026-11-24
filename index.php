<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$busca       = trim($_GET['busca'] ?? '');
$categoriaId = (int) ($_GET['categoria'] ?? 0);

$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome')->fetchAll();

$sql = 'SELECT p.*, c.nome AS categoria_nome
        FROM produtos p
        INNER JOIN categorias c ON c.id = p.categoria_id
        WHERE p.ativo = 1';
$params = [];

if ($categoriaId > 0) {
    $sql .= ' AND p.categoria_id = ?';
    $params[] = $categoriaId;
}

if ($busca !== '') {
    $sql .= ' AND (p.nome LIKE ? OR p.descricao LIKE ?)';
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

$sql .= ' ORDER BY p.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();
?>

<div class="row mb-4 align-items-end">
    <div class="col-md-5">
        <h2 class="mb-0">Catálogo de Livros</h2>
        <small class="text-muted">Escolha seus títulos e adicione ao carrinho</small>
    </div>
    <div class="col-md-7">
        <form method="GET" class="row g-2">
            <div class="col-md-5">
                <select name="categoria" class="form-select">
                    <option value="0">Todas as categorias</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoriaId === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="busca" class="form-control" placeholder="Buscar livro..." value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($produtos)): ?>
        <div class="col-12">
            <div class="alert alert-secondary">Nenhum livro encontrado para esse filtro.</div>
        </div>
    <?php endif; ?>

    <?php foreach ($produtos as $livro): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
                <img src="<?= htmlspecialchars($livro['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($livro['nome']) ?>" style="height: 240px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-secondary mb-2 align-self-start"><?= htmlspecialchars($livro['categoria_nome']) ?></span>
                    <h5 class="card-title"><?= htmlspecialchars($livro['nome']) ?></h5>
                    <p class="fw-bold text-success fs-5 mb-1">R$ <?= number_format($livro['preco'], 2, ',', '.') ?></p>
                    <p class="small text-muted mb-3">
                        <?= $livro['estoque'] > 0 ? "Em estoque: {$livro['estoque']} un." : '<span class="text-danger">Esgotado</span>' ?>
                    </p>
                    <div class="mt-auto d-grid gap-2">
                        <a href="produto.php?id=<?= $livro['id'] ?>" class="btn btn-outline-dark btn-sm">Ver detalhes</a>
                        <?php if ($livro['estoque'] > 0): ?>
                            <form action="actions/carrinho_acoes.php" method="POST">
                                <input type="hidden" name="acao" value="adicionar">
                                <input type="hidden" name="produto_id" value="<?= $livro['id'] ?>">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Adicionar ao carrinho</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>