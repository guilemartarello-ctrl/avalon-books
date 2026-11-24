<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensagem'] = ['tipo' => 'warning', 'texto' => 'Faça login para concluir a compra.'];
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['carrinho'])) {
    header('Location: ../carrinho.php');
    exit;
}

$formaPagamento = $_POST['forma_pagamento'] ?? 'PIX';
$formasPermitidas = ['PIX', 'Boleto', 'Cartao'];
if (!in_array($formaPagamento, $formasPermitidas, true)) {
    $formaPagamento = 'PIX';
}

try {
    $pdo->beginTransaction();

    $totalPedido = 0;
    $itensParaGravar = [];

    $stmtProduto = $pdo->prepare('SELECT id, nome, preco, estoque FROM produtos WHERE id = ? AND ativo = 1 FOR UPDATE');

    foreach ($_SESSION['carrinho'] as $produtoId => $qtd) {
        $stmtProduto->execute([(int)$produtoId]);
        $produto = $stmtProduto->fetch();

        if (!$produto || $produto['estoque'] < $qtd) {
            throw new Exception('Estoque insuficiente para um dos itens do carrinho.');
        }

        $subtotal = $produto['preco'] * $qtd;
        $totalPedido += $subtotal;

        $itensParaGravar[] = [
            'produto_id'     => $produto['id'],
            'quantidade'     => $qtd,
            'preco_unitario' => $produto['preco'],
        ];
    }

    // 1. Criação do registro de pedido
    $stmtPedido = $pdo->prepare('INSERT INTO pedidos (usuario_id, forma_pagamento, total, status) VALUES (?, ?, ?, ?)');
    $stmtPedido->execute([$_SESSION['usuario']['id'], $formaPagamento, $totalPedido, 'Pago']);
    $pedidoId = $pdo->lastInsertId();

    // 2. Registro detalhado de cada item + 3. Decremento automático no estoque
    $stmtItem    = $pdo->prepare('INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)');
    $stmtEstoque = $pdo->prepare('UPDATE produtos SET estoque = estoque - ? WHERE id = ?');

    foreach ($itensParaGravar as $item) {
        $stmtItem->execute([$pedidoId, $item['produto_id'], $item['quantidade'], $item['preco_unitario']]);
        $stmtEstoque->execute([$item['quantidade'], $item['produto_id']]);
    }

    $pdo->commit();

    // 4. Limpeza do carrinho na sessão
    unset($_SESSION['carrinho']);

    $_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => "Pedido #$pedidoId confirmado via $formaPagamento! Estoque atualizado."];
    header('Location: ../index.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Erro ao finalizar pedido: ' . $e->getMessage()];
    header('Location: ../carrinho.php');
    exit;
}