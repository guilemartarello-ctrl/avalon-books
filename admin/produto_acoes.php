<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$acao = $_POST['acao'] ?? '';

if ($acao === 'salvar') {
    $id          = (int) ($_POST['id'] ?? 0);
    $categoriaId = (int) ($_POST['categoria_id'] ?? 1);
    $nome        = trim($_POST['nome'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');
    $preco       = (float) ($_POST['preco'] ?? 0);
    $estoque     = max(0, (int) ($_POST['estoque'] ?? 0));
    $imagem      = trim($_POST['imagem'] ?? 'https://placehold.co/300x400?text=Livro');

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE produtos SET categoria_id = ?, nome = ?, descricao = ?, preco = ?, estoque = ?, imagem = ? WHERE id = ?');
        $stmt->execute([$categoriaId, $nome, $descricao, $preco, $estoque, $imagem, $id]);
        $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Produto atualizado com sucesso!'];
    } else {
        $stmt = $pdo->prepare('INSERT INTO produtos (categoria_id, nome, descricao, preco, estoque, imagem) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$categoriaId, $nome, $descricao, $preco, $estoque, $imagem]);
        $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Produto cadastrado com sucesso!'];
    }
} elseif ($acao === 'alternar_status') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE produtos SET ativo = NOT ativo WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['mensagem'] = ['tipo' => 'info', 'texto' => 'Status do produto alterado!'];
    }
}

header('Location: index.php');
exit;