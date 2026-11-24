<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

$stmt = $pdo->prepare('SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha, $usuario['senha'])) {
    $_SESSION['mensagem'] = ['tipo' => 'danger', 'texto' => 'E-mail ou senha inválidos.'];
    header('Location: ../login.php');
    exit;
}

$_SESSION['usuario'] = [
    'id'     => $usuario['id'],
    'nome'   => $usuario['nome'],
    'email'  => $usuario['email'],
    'perfil' => $usuario['perfil'],
];

$_SESSION['mensagem'] = ['tipo' => 'success', 'texto' => 'Login realizado com sucesso!'];
header('Location: ' . ($usuario['perfil'] === 'admin' ? '../admin/index.php' : '../index.php'));
exit;