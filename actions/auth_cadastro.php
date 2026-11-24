<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cadastro.php');
    exit;
}

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($nome === '' || $email === '' || strlen($senha) < 4) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Preencha todos os campos (senha mínima de 4 caracteres).'];
    header('Location: ../cadastro.php');
    exit;
}

// Verifica se o e-mail já existe usando Prepared Statement
$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
$stmt->execute([$email]);

if ($stmt->fetch()) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'Este e-mail já está cadastrado.'];
    header('Location: ../cadastro.php');
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)');
$stmt->execute([$nome, $email, $senhaHash, 'cliente']);

$_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Cadastro realizado! Faça seu login.'];
header('Location: ../login.php');
exit;