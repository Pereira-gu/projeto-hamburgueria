<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Acesso inválido.');
}

$id = $_POST['id'];
$nome = trim($_POST['nome']);

if (empty($id) || empty($nome)) {
    header('Location: ' . BASE_URL . '/admin/gerenciar_categorias.php?status=erro');
    exit();
}

try {
    $sql = "UPDATE categorias SET nome = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $id]);
    
    header('Location: ' . BASE_URL . '/admin/gerenciar_categorias.php?status=editado');
    exit();

} catch (PDOException $e) {
    error_log("Erro ao atualizar categoria: " . $e->getMessage());
    die("Erro ao atualizar a categoria. Pode ser que o nome já exista.");
}
?>