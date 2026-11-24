<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$acao       = $_POST['acao'] ?? $_GET['acao'] ?? '';
$produtoId  = (int) ($_POST['produto_id'] ?? $_GET['produto_id'] ?? 0);
$quantidade = max(1, (int) ($_POST['quantidade'] ?? 1));

if ($produtoId > 0) {
    $stmt = $pdo->prepare('SELECT id, estoque, ativo FROM produtos WHERE id = ?');
    $stmt->execute([$produtoId]);
    $produto = $stmt->fetch();

    if ($produto && (int)$produto['ativo'] === 1) {
        $estoqueDisponivel = (int) $produto['estoque'];

        if ($acao === 'adicionar') {
            $atual = $_SESSION['carrinho'][$produtoId] ?? 0;
            $novaQtd = min($estoqueDisponivel, $atual + $quantidade);
            if ($estoqueDisponivel > 0) {
                $_SESSION['carrinho'][$produtoId] = $novaQtd;
                $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Livro adicionado ao carrinho!'];
            } else {
                $_SESSION['mensagem'] = ['tipo' => 'warning', 'texto' => 'Produto sem estoque no momento.'];
            }
        } elseif ($acao === 'atualizar') {
            if ($quantidade > $estoqueDisponivel) {
                $_SESSION['carrinho'][$produtoId] = $estoqueDisponivel;
                $_SESSION['mensagem'] = ['tipo' => 'warning', 'texto' => 'Quantidade ajustada ao limite do estoque disponível.'];
            } else {
                $_SESSION['carrinho'][$produtoId] = $quantidade;
                $_SESSION['mensagem'] = ['tipo' => 'info', 'texto' => 'Quantidade atualizada.'];
            }
        } elseif ($acao === 'remover') {
            unset($_SESSION['carrinho'][$produtoId]);
            $_SESSION['mensagem'] = ['tipo' => 'info', 'texto' => 'Item removido do carrinho.'];
        }
    }
}

header('Location: ../carrinho.php');
exit;