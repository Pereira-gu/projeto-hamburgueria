<?php
session_start();
require_once 'conexao.php';

// Define o cabeçalho como JSON para a resposta AJAX
header('Content-Type: application/json');

// Função para enviar resposta JSON e encerrar o script
function json_response($data) {
    echo json_encode($data);
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    json_response(['sucesso' => false, 'mensagem' => 'Erro de validação de segurança.']);
}

if (!isset($_SESSION['usuario_id'])) {
    json_response(['sucesso' => false, 'mensagem' => 'Login necessário.', 'redirecionar' => BASE_URL . '/cadastro.php']);
}

if (!isset($_POST['produto_id'])) {
    json_response(['sucesso' => false, 'mensagem' => 'Produto não especificado.']);
}

$id_usuario = $_SESSION['usuario_id'];
$id_produto = $_POST['produto_id'];
$opcionais_post = $_POST['opcionais'] ?? [];
$quantidade_adicionar = 1; 

$detalhes_personalizacao = [];
$ids_opcionais = [];

if (!empty($opcionais_post)) {
    foreach ($opcionais_post as $grupo => $opcionais_do_grupo) {
        foreach ($opcionais_do_grupo as $opcional_id) {
            $ids_opcionais[] = $opcional_id;
        }
    }
    
    if (!empty($ids_opcionais)) {
        $placeholders = implode(',', array_fill(0, count($ids_opcionais), '?'));
        $stmt_opc = $pdo->prepare("SELECT nome, preco FROM opcionais WHERE id IN ($placeholders)");
        $stmt_opc->execute($ids_opcionais);
        $detalhes_personalizacao = $stmt_opc->fetchAll(PDO::FETCH_ASSOC);
    }
}

$personalizacao_json = !empty($detalhes_personalizacao) ? json_encode($detalhes_personalizacao) : null;

try {
    $sql_check = "SELECT * FROM carrinho WHERE login_id = ? AND produtos_id = ? AND (personalizacao = ? OR (personalizacao IS NULL AND ? IS NULL))";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$id_usuario, $id_produto, $personalizacao_json, $personalizacao_json]);
    $item_existente = $stmt_check->fetch();

    if ($item_existente) {
        $nova_quantidade = $item_existente['quantidade'] + $quantidade_adicionar;
        $sql_update = "UPDATE carrinho SET quantidade = ? WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nova_quantidade, $item_existente['id']]);
    } else {
        $sql_insert = "INSERT INTO carrinho (login_id, produtos_id, quantidade, personalizacao) VALUES (?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$id_usuario, $id_produto, $quantidade_adicionar, $personalizacao_json]);
    }

    // Calcula o novo total de itens no carrinho
    $stmt_total = $pdo->prepare("SELECT SUM(quantidade) as total FROM carrinho WHERE login_id = ?");
    $stmt_total->execute([$id_usuario]);
    $novo_total = $stmt_total->fetchColumn();

    json_response(['sucesso' => true, 'mensagem' => 'Item adicionado!', 'total_carrinho' => $novo_total]);

} catch (PDOException $e) {
    error_log("Erro ao adicionar ao carrinho (AJAX): " . $e->getMessage());
    json_response(['sucesso' => false, 'mensagem' => 'Erro ao conectar com o servidor.']);
}
?>