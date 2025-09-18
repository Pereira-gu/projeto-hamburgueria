<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php'; // Segurança

header('Content-Type: application/json');

// Pega o ID do último pedido que o admin já viu, enviado pelo JavaScript
$ultimo_id_conhecido = $_GET['ultimo_id'] ?? 0;

try {
    // Busca todos os pedidos MAIS RECENTES que o último ID conhecido
    $sql = "SELECT p.id, p.valor_total, p.data_pedido, p.status, l.nome as cliente_nome
            FROM pedidos p
            JOIN login l ON p.login_id = l.id
            WHERE p.id > ?
            ORDER BY p.id DESC"; // Ordena para que os mais novos venham primeiro

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ultimo_id_conhecido]);
    $novos_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envia os novos pedidos (se houver) como resposta JSON
    echo json_encode($novos_pedidos);
} catch (PDOException $e) {
    // Em caso de erro, retorna um JSON de erro
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor.']);
}
