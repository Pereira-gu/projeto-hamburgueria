<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

$pedido_id = $_GET['id'] ?? null;
if (!$pedido_id) {
    header("Location: " . BASE_URL . "/admin/gerenciar_pedidos.php");
    exit();
}

try {
    // Busca os dados gerais do pedido
    $sql_pedido = "SELECT p.*, l.nome as cliente_nome, l.email as cliente_email FROM pedidos p JOIN login l ON p.login_id = l.id WHERE p.id = ?";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->execute([$pedido_id]);
    $pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        die("Pedido não encontrado.");
    }

    // Busca os itens do pedido
    $sql_itens = "SELECT pi.quantidade, pi.preco_unitario, pi.personalizacao, pr.nome_produto FROM pedido_itens pi JOIN produtos pr ON pi.produto_id = pr.id WHERE pi.pedido_id = ?";
    $stmt_itens = $pdo->prepare($sql_itens);
    $stmt_itens->execute([$pedido_id]);
    $itens_pedido = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar detalhes do pedido: " . $e->getMessage());
}
?>

<div class="container page-content">
    <h1>Detalhes do Pedido #<?php echo $pedido['id']; ?></h1>
    <a href="gerenciar_pedidos.php">&larr; Voltar para todos os pedidos</a>

    <div class="detalhe-pedido-grid">
        <div class="card-admin">
            <h2>Itens do Pedido</h2>
            <table class="itens-table" style="width: 100%; margin-top: 15px;">
                <tbody>
                    <?php foreach($itens_pedido as $item): ?>
                        <tr>
                            <td>
                                <span class="item-principal"><?php echo $item['quantidade']; ?>x <?php echo htmlspecialchars($item['nome_produto']); ?></span>
                                <?php 
                                $personalizacao = json_decode($item['personalizacao'], true);
                                if (!empty($personalizacao)):
                                ?>
                                    <div class="detalhes-personalizacao">
                                        <?php foreach($personalizacao as $opcional): ?>
                                            + <?php echo htmlspecialchars($opcional['nome']); ?><br>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: bold;">
                                R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                        <tr style="border-top: 2px solid #333;">
                            <td style="font-weight: bold; font-size: 1.2rem;">Total do Pedido</td>
                            <td style="text-align: right; font-weight: bold; font-size: 1.2rem;">
                                R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
        
        <div class="card-admin">
            <h2>Informações do Cliente</h2>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($pedido['cliente_nome']); ?></p>
            <hr>
            <h2>Dados de Entrega</h2>
            <p><strong>Tipo:</strong> <?php echo ucfirst($pedido['tipo_entrega']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($pedido['telefone_contato']); ?></p>
            <?php if ($pedido['tipo_entrega'] == 'delivery'): ?>
                <p><strong>Endereço:</strong><br><?php echo nl2br(htmlspecialchars($pedido['endereco_entrega'])); ?></p>
            <?php endif; ?>
            <hr>
            <h2>Pagamento</h2>
            <p><strong>Método:</strong> <?php echo htmlspecialchars($pedido['metodo_pagamento']); ?></p>
            <?php if ($pedido['metodo_pagamento'] == 'Dinheiro' && !empty($pedido['troco_para'])): ?>
                <p><strong>Troco para:</strong> R$ <?php echo number_format($pedido['troco_para'], 2, ',', '.'); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($pedido['observacoes'])): ?>
                <hr>
                <h2>Observações do Cliente</h2>
                <p><?php echo nl2br(htmlspecialchars($pedido['observacoes'])); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div style="margin-top:20px;">
        <a href="imprimir_pedido.php?id=<?php echo $pedido['id']; ?>" target="_blank" class="btn-login">Imprimir Pedido</a>
    </div>
</div>

<?php 
require_once __DIR__ . '/../../app/templates/admin_footer.php'; 
?>