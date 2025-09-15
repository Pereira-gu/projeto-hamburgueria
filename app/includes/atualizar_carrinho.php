<?php
session_start();
require_once __DIR__ . '/conexao.php'; // Caminho ajustado para a pasta 'includes'

header('Content-Type: application/json');

function json_response($data) {
    echo json_encode($data);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    json_response(['sucesso' => false, 'mensagem' => 'Acesso inválido.']);
}
if (!isset($_SESSION['usuario_id'])) {
    json_response(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
}

$id_usuario = $_SESSION['usuario_id'];
$id_carrinho = $_POST['carrinho_id'];
$nova_quantidade = (int)$_POST['quantidade'];

if ($nova_quantidade < 0) {
    json_response(['sucesso' => false, 'mensagem' => 'Quantidade inválida.']);
}

try {
    $pdo->beginTransaction();

    if ($nova_quantidade == 0) {
        $stmt_delete = $pdo->prepare("DELETE FROM carrinho WHERE id = ? AND login_id = ?");
        $stmt_delete->execute([$id_carrinho, $id_usuario]);
    } else {
        $stmt_update = $pdo->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ? AND login_id = ?");
        $stmt_update->execute([$nova_quantidade, $id_carrinho, $id_usuario]);
    }

    // Recalcular todos os totais
    $sql_carrinho = "SELECT p.preco_produto, c.quantidade, c.personalizacao FROM carrinho c JOIN produtos p ON c.produtos_id = p.id WHERE c.login_id = ?";
    $stmt_carrinho = $pdo->prepare($sql_carrinho);
    $stmt_carrinho->execute([$id_usuario]);
    $itens_carrinho = $stmt_carrinho->fetchAll(PDO::FETCH_ASSOC);

    $total_geral = 0;
    $total_itens = 0;
    foreach ($itens_carrinho as $item) {
        $preco_item = $item['preco_produto'];
        $personalizacao = json_decode($item['personalizacao'], true);
        if ($personalizacao) {
            foreach($personalizacao as $opcional) $preco_item += $opcional['preco'];
        }
        $total_geral += $preco_item * $item['quantidade'];
        $total_itens += $item['quantidade'];
    }

    // Recalcula o subtotal do item específico que foi alterado
    $subtotal_item = 0;
    if ($nova_quantidade > 0) {
        $stmt_item_preco = $pdo->prepare("SELECT p.preco_produto, c.personalizacao FROM carrinho c JOIN produtos p ON c.produtos_id = p.id WHERE c.id = ?");
        $stmt_item_preco->execute([$id_carrinho]);
        $item_atualizado = $stmt_item_preco->fetch(PDO::FETCH_ASSOC);

        $preco_unitario = $item_atualizado['preco_produto'];
        $personalizacao_item = json_decode($item_atualizado['personalizacao'], true);
        if ($personalizacao_item) {
            foreach($personalizacao_item as $opcional) $preco_unitario += $opcional['preco'];
        }
        $subtotal_item = $preco_unitario * $nova_quantidade;
    }

    $pdo->commit();
    json_response([
        'sucesso' => true,
        'novo_subtotal_formatado' => 'R$ ' . number_format($subtotal_item, 2, ',', '.'),
        'novo_total_formatado' => 'R$ ' . number_format($total_geral, 2, ',', '.'),
        'total_itens_carrinho' => $total_itens,
        'item_removido' => $nova_quantidade == 0,
        'carrinho_vazio' => empty($itens_carrinho)
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao atualizar carrinho (AJAX): " . $e->getMessage());
    json_response(['sucesso' => false, 'mensagem' => 'Erro de servidor.']);
}