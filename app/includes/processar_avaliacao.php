<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';

// Segurança
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Acesso inválido.');
}
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$id_pedido = $_POST['pedido_id'];
$notas = $_POST['notas'] ?? [];
$comentarios = $_POST['comentarios'] ?? [];

if (empty($id_pedido) || empty($notas)) {
    header('Location: ' . BASE_URL . '/meus_pedidos.php?erro=dados_insuficientes');
    exit();
}

$pdo->beginTransaction();
try {
    // Verifica se o pedido pertence ao usuário e pode ser avaliado
    $stmt_check = $pdo->prepare("SELECT id FROM pedidos WHERE id = ? AND login_id = ? AND status = 'Concluído'");
    $stmt_check->execute([$id_pedido, $id_usuario]);
    if ($stmt_check->rowCount() == 0) {
        throw new Exception("Pedido inválido para avaliação.");
    }
    
    // Insere cada avaliação no banco de dados
    $sql = "INSERT INTO avaliacoes (produto_id, usuario_id, pedido_id, nota, comentario) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    foreach ($notas as $produto_id => $nota) {
        if (!empty($nota)) {
            $comentario = $comentarios[$produto_id] ?? null;
            $stmt->execute([$produto_id, $id_usuario, $id_pedido, $nota, $comentario]);
        }
    }

    $pdo->commit();
    header('Location: ' . BASE_URL . '/meus_pedidos.php?status=avaliacao_ok');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Erro ao processar avaliação: " . $e->getMessage());
    header('Location: ' . BASE_URL . '/avaliar_pedido.php?pedido_id=' . $id_pedido . '&erro=db');
    exit();
}
?>