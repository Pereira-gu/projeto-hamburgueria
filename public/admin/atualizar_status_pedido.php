<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $novo_status = $_POST['status'] ?? null;
    
    // Lista de status válidos para evitar injeção de dados
    $statuses_validos = ['Pendente', 'Em Preparo', 'Saiu para Entrega', 'Concluído', 'Cancelado'];

    // Verifica se os dados recebidos são válidos
    if (empty($pedido_id) || empty($novo_status) || !in_array($novo_status, $statuses_validos)) {
        // Redireciona com erro se os dados forem inválidos
        header("Location: " . BASE_URL . "/admin/gerenciar_pedidos.php?status=erro_dados_invalidos");
        exit();
    }

    try {
        $sql = "UPDATE pedidos SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$novo_status, $pedido_id]);

        // Redireciona de volta para a página de pedidos com sucesso
        header("Location: " . BASE_URL . "/admin/gerenciar_pedidos.php?status=atualizado_sucesso");
        exit();

    } catch (PDOException $e) {
        error_log("Erro ao atualizar status do pedido: " . $e->getMessage());
        // Redireciona com erro se houver falha no banco de dados
        header("Location: " . BASE_URL . "/admin/gerenciar_pedidos.php?status=erro_db");
        exit();
    }
} else {
    // Se não for um POST, apenas redireciona para a página principal de pedidos
    header("Location: " . BASE_URL . "/admin/gerenciar_pedidos.php");
    exit();
}
?>