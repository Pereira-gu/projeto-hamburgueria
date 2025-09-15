<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if (!isset($_GET['token']) || !isset($_SESSION['csrf_token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação de segurança.');
}

$id_opcional = $_GET['id'] ?? null;

if (!$id_opcional) {
    header("Location: " . BASE_URL . "/admin/gerenciar_opcionais.php");
    exit();
}

try {
    $pdo->beginTransaction();

    // Primeiro, remove as associações com produtos para evitar erros
    $stmt1 = $pdo->prepare("DELETE FROM produto_opcionais WHERE opcional_id = ?");
    $stmt1->execute([$id_opcional]);

    // Depois, exclui o opcional em si
    $stmt2 = $pdo->prepare("DELETE FROM opcionais WHERE id = ?");
    $stmt2->execute([$id_opcional]);
    
    $pdo->commit();

    header("Location: " . BASE_URL . "/admin/gerenciar_opcionais.php?status=excluido");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir opcional: " . $e->getMessage());
    header("Location: " . BASE_URL . "/admin/gerenciar_opcionais.php?status=erro_excluir");
    exit();
}
?>