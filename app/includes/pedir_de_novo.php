<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';

// Segurança básica
if (!isset($_SESSION['usuario_id']) || !isset($_GET['pedido_id']) || !isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$id_pedido = $_GET['pedido_id'];

$pdo->beginTransaction();

try {
    // 1. Busca os itens do pedido antigo
    $sql_itens = "SELECT produto_id, quantidade FROM pedido_itens WHERE pedido_id = ?";
    $stmt_itens = $pdo->prepare($sql_itens);
    $stmt_itens->execute([$id_pedido]);
    $itens_antigos = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

    if (empty($itens_antigos)) {
        throw new Exception("Pedido antigo não encontrado ou sem itens.");
    }

    // 2. Itera sobre cada item e o adiciona ao carrinho atual
    foreach ($itens_antigos as $item) {
        $id_produto = $item['produto_id'];
        $quantidade_adicionar = $item['quantidade'];

        // Verifica se o item já existe no carrinho atual
        $sql_check = "SELECT * FROM carrinho WHERE login_id = ? AND produtos_id = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id_usuario, $id_produto]);
        $item_existente = $stmt_check->fetch();

        if ($item_existente) {
            // Se existe, atualiza a quantidade
            $nova_quantidade = $item_existente['quantidade'] + $quantidade_adicionar;
            $sql_update = "UPDATE carrinho SET quantidade = ? WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$nova_quantidade, $item_existente['id']]);
        } else {
            // Se não existe, insere um novo registro
            $sql_insert = "INSERT INTO carrinho (login_id, produtos_id, quantidade) VALUES (?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([$id_usuario, $id_produto, $quantidade_adicionar]);
        }
    }

    $pdo->commit();
    // 3. Redireciona o usuário para o carrinho para ele finalizar a compra
    header("Location: " . BASE_URL . "/carrinho.php?status=pedido_replicado");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Erro ao pedir de novo: " . $e->getMessage());
    header("Location: " . BASE_URL . "/meus_pedidos.php?erro=replicar");
    exit();
}
?>