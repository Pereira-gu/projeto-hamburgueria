<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Acesso inválido.');
}

$id = $_POST['id'];
$grupo = trim($_POST['grupo']);
$nome = trim($_POST['nome']);
$preco = $_POST['preco'];

if (empty($id) || empty($grupo) || empty($nome)) {
    // Redireciona com erro se algum campo estiver vazio
    header('Location: ' . BASE_URL . '/admin/editar_opcional.php?id=' . $id . '&erro=campos');
    exit();
}

try {
    $sql = "UPDATE opcionais SET grupo = ?, nome = ?, preco = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$grupo, $nome, $preco, $id]);
    
    header('Location: ' . BASE_URL . '/admin/gerenciar_opcionais.php?status=editado');
    exit();

} catch (PDOException $e) {
    error_log("Erro ao atualizar opcional: " . $e->getMessage());
    die("Erro ao atualizar o opcional. Por favor, tente novamente.");
}
?>