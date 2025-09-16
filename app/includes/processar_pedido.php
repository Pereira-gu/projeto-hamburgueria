<?php
session_start();
require_once 'conexao.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação CSRF.');
}

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$tipo_entrega = $_POST['tipo_entrega'] ?? 'delivery';

// --- NOVAS VARIÁVEIS RECEBIDAS DO FORMULÁRIO ---
$metodo_pagamento = $_POST['metodo_pagamento'] ?? 'Não informado';
$troco_para = !empty($_POST['troco_para']) ? $_POST['troco_para'] : null;
$observacoes = trim($_POST['observacoes']) ?? null;

if ($tipo_entrega == 'delivery') {
    $endereco_entrega = trim($_POST['endereco_entrega']);
    $telefone_contato = trim($_POST['telefone_contato']);
    if (empty($endereco_entrega) || empty($telefone_contato)) {
        header('Location: ' . BASE_URL . '/checkout.php?status=erro_dados');
        exit();
    }
} else {
    // CORREÇÃO PRINCIPAL NO BACK-END:
    // Define um valor padrão para o endereço e pega o telefone do campo correto.
    $endereco_entrega = 'Retirada no Local';
    $telefone_contato = trim($_POST['telefone_retirada']);
    if (empty($telefone_contato)) {
        header('Location: ' . BASE_URL . '/checkout.php?status=erro_dados');
        exit();
    }
}

$pdo->beginTransaction();
try {
    // 1. Busca os itens do carrinho (código existente)
    $sql_carrinho = "SELECT p.id as produto_id, p.preco_produto, c.quantidade, c.personalizacao FROM carrinho c JOIN produtos p ON c.produtos_id = p.id WHERE c.login_id = ?";
    $stmt_carrinho = $pdo->prepare($sql_carrinho);
    $stmt_carrinho->execute([$id_usuario]);
    $itens_carrinho = $stmt_carrinho->fetchAll(PDO::FETCH_ASSOC);

    if (empty($itens_carrinho)) {
        $pdo->rollBack();
        header('Location: ' . BASE_URL . '/carrinho.php?status=carrinho_vazio');
        exit();
    }

    // 2. Calcula o valor total (código existente)
    $valor_total = 0;
    foreach ($itens_carrinho as $item) {
        $preco_item_personalizado = $item['preco_produto'];
        $personalizacao_array = json_decode($item['personalizacao'], true);
        if (!empty($personalizacao_array)) {
            foreach ($personalizacao_array as $opcional) {
                $preco_item_personalizado += $opcional['preco'];
            }
        }
        $valor_total += $preco_item_personalizado * $item['quantidade'];
    }

    // 3. --- SQL ATUALIZADO PARA INCLUIR OS NOVOS CAMPOS ---
    $sql_pedido = "INSERT INTO pedidos (login_id, valor_total, endereco_entrega, telefone_contato, status, tipo_entrega, metodo_pagamento, troco_para, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    // A linha abaixo é a importante. Verifique se as variáveis estão corretas.
    // ...
    $stmt_pedido->execute([$id_usuario, $valor_total, $endereco_entrega, $telefone_contato, 'Pendente', $tipo_entrega, $metodo_pagamento, $troco_para, $observacoes]);
    // ...

    $id_pedido = $pdo->lastInsertId();

    // 4. Insere os itens na tabela 'pedido_itens' (código existente)
    $sql_item = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, personalizacao) VALUES (?, ?, ?, ?, ?)";
    $stmt_item = $pdo->prepare($sql_item);
    foreach ($itens_carrinho as $item) {
        $preco_unitario_final = $item['preco_produto'];
        $personalizacao_array_item = json_decode($item['personalizacao'], true);
        if (!empty($personalizacao_array_item)) {
            foreach ($personalizacao_array_item as $opcional) {
                $preco_unitario_final += $opcional['preco'];
            }
        }
        $stmt_item->execute([$id_pedido, $item['produto_id'], $item['quantidade'], $preco_unitario_final, $item['personalizacao']]);
    }

    // 5. Limpa o carrinho (código existente)
    $sql_limpar = "DELETE FROM carrinho WHERE login_id = ?";
    $stmt_limpar = $pdo->prepare($sql_limpar);
    $stmt_limpar->execute([$id_usuario]);

    $pdo->commit();

    header("Location: " . BASE_URL . "/pedido_sucesso.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Erro ao processar pedido: " . $e->getMessage());
    die("Erro ao processar o pedido. Por favor, tente novamente.");
}
