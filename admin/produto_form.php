<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$produto = [
    'id'           => 0,
    'categoria_id' => 1,
    'nome'         => '',
    'descricao'    => '',
    'preco'        => '',
    'estoque'      => 0,
    'imagem'       => 'https://placehold.co/300x400?text=Livro',
];

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
    $stmt->execute([$id]);
    $encontrado = $stmt->fetch();
    if ($encontrado) {
        $produto = $encontrado;
    }
}

$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome')->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><?= $id > 0 ? 'Editar Produto #' . $id : 'Cadastrar Novo Produto' ?></h4>
            </div>
            <div class="card-body">
                <form action="produto_acoes.php" method="POST">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Nome do Livro</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria_id" class="form-select" required>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (int)$produto['categoria_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" min="0.01" name="preco" class="form-control" value="<?= htmlspecialchars($produto['preco']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estoque</label>
                            <input type="number" min="0" name="estoque" class="form-control" value="<?= htmlspecialchars($produto['estoque']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL da Imagem</label>
                        <input type="text" name="imagem" class="form-control" value="<?= htmlspecialchars($produto['imagem']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" rows="3" class="form-control"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>