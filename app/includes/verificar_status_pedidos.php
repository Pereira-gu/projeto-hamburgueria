<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';

// Define o cabeçalho como JSON para a resposta
header('Content-Type: application/json');

// Função para enviar resposta JSON e encerrar o script
function json_response($data)
{
    echo json_encode($data);
    exit();
}

// Segurança: Garante que o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    json_response(['sucesso' => false, 'erro' => 'Usuário não autenticado.']);
}

// Recebe os dados enviados pelo JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$pedido_ids = $input['pedido_ids'] ?? [];

// Validação: Garante que recebemos uma lista de IDs
if (empty($pedido_ids) || !is_array($pedido_ids)) {
    json_response([]); // Retorna um array vazio se não houver IDs
}

$id_usuario = $_SESSION['usuario_id'];

try {
    // Cria os placeholders (?) para a consulta SQL, ex: (?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($pedido_ids), '?'));

    // A consulta busca o ID e o status dos pedidos que pertencem ao usuário logado
    $sql = "SELECT id, status FROM pedidos WHERE login_id = ? AND id IN ($placeholders)";

    $stmt = $pdo->prepare($sql);

    // Junta o ID do usuário com a lista de IDs de pedidos para o execute()
    $params = array_merge([$id_usuario], $pedido_ids);
    $stmt->execute($params);

    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formata a resposta para ser um array 'id_pedido' => 'status'
    $resposta = [];
    foreach ($statuses as $row) {
        $resposta[$row['id']] = $row['status'];
    }

    json_response($resposta);
} catch (PDOException $e) {
    error_log("Erro ao verificar status de pedidos: " . $e->getMessage());
    json_response(['sucesso' => false, 'erro' => 'Erro no servidor.']);
}
