<?php
session_start();
require_once 'conexao.php';

if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação de segurança.');
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/cadastro.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "/carrinho.php?status=erro");
    exit();
}

$id_item_carrinho = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

try {
    $sql = "DELETE FROM carrinho WHERE id = ? AND login_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_item_carrinho, $id_usuario]);

    header("Location: " . BASE_URL . "/carrinho.php?status=removido");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao remover do carrinho: " . $e->getMessage());
    header("Location: " . BASE_URL . "/carrinho.php?status=erro_db");
    exit();
}
?>