<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

$pedido_id = $_GET['id'] ?? null;
if (!$pedido_id) die("ID do pedido não fornecido.");

try {
    $sql_pedido = "SELECT p.*, l.nome as cliente_nome FROM pedidos p JOIN login l ON p.login_id = l.id WHERE p.id = ?";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([$pedido_id]);
    $pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

    // Busca itens incluindo a personalização
    $sql_itens = "SELECT pi.quantidade, pi.personalizacao, pr.nome_produto 
                  FROM pedido_itens pi 
                  JOIN produtos pr ON pi.produto_id = pr.id 
                  WHERE pi.pedido_id = ?";
    $stmt_itens = $pdo->prepare($sql_itens);
    $stmt_itens->execute([$pedido_id]);
    $itens_pedido = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar o pedido: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedido #<?php echo $pedido['id']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; color: #000; }
        .item-principal { font-weight: bold; font-size: 1.1em; }
        .detalhes-opcionais { padding-left: 15px; font-size: 0.9em; }
        hr { border: none; border-top: 1px dashed #000; }
        @media print { button { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h1 style="text-align: center;">Saboroso Burger</h1>
    <h2 style="text-align: center;">Pedido #<?php echo $pedido['id']; ?></h2>
    <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['cliente_nome']); ?></p>
    <hr>
    <h3>Itens do Pedido</h3>
    <?php foreach($itens_pedido as $item): ?>
        <p class="item-principal"><?php echo $item['quantidade']; ?>x <?php echo htmlspecialchars($item['nome_produto']); ?></p>
        <?php 
        $personalizacao = json_decode($item['personalizacao'], true);
        if (!empty($personalizacao)):
        ?>
            <div class="detalhes-opcionais">
                <?php foreach($personalizacao as $opcional): ?>
                    &nbsp;&nbsp;+ <?php echo htmlspecialchars($opcional['nome']); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <hr>
    <?php if ($pedido['tipo_entrega'] == 'delivery'): ?>
        <h3>ENTREGA</h3>
        <p><?php echo nl2br(htmlspecialchars($pedido['endereco_entrega'])); ?></p>
        <p><strong>Tel:</strong> <?php echo htmlspecialchars($pedido['telefone_contato']); ?></p>
    <?php else: ?>
        <h3 style="text-align: center;">** RETIRADA NO LOCAL **</h3>
        <p><strong>Tel:</strong> <?php echo htmlspecialchars($pedido['telefone_contato']); ?></p>
    <?php endif; ?>
    <hr>
    <p style="text-align: center;">Obrigado!</p>
    <button onclick="window.print()" style="width: 100%; padding: 10px; margin-top: 20px;">Imprimir</button>
</body>
</html>