<?php
session_start();
unset($_SESSION['usuario']);
$_SESSION['mensagem'] = ['tipo' => 'info', 'texto' => 'Você saiu da sua conta.'];
header('Location: ../index.php');
exit;