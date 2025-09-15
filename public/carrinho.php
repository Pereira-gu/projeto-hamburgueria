<?php
require_once __DIR__ . '/../app/templates/header.php';
require_once __DIR__ . '/../app/includes/conexao.php';

// --- LÓGICA DO HORÁRIO DE FUNCIONAMENTO (EXISTENTE) ---
$loja_aberta = false;
try {
    $stmt_config = $pdo->query("SELECT * FROM configuracoes WHERE chave IN ('HORARIO_ABERTURA', 'HORARIO_FECHAMENTO')");
    $configs = $stmt_config->fetchAll(PDO::FETCH_KEY_PAIR);
    $abertura = $configs['HORARIO_ABERTURA'] ?? '00:00';
    $fechamento = $configs['HORARIO_FECHAMENTO'] ?? '23:59';
    date_default_timezone_set('America/Sao_Paulo');
    $hora_atual = date('H:i');
    if ($hora_atual >= $abertura && $hora_atual < $fechamento) $loja_aberta = true;
} catch (PDOException $e) {
    $loja_aberta = true;
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/cadastro.php');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$total_carrinho = 0;

try {
    $sql = "SELECT p.nome_produto, p.preco_produto, p.imagem, c.id AS carrinho_id, c.quantidade, c.personalizacao FROM carrinho c JOIN produtos p ON c.produtos_id = p.id WHERE c.login_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario]);
    $itens_carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar o carrinho: " . $e->getMessage());
}
?>

<div class="container page-content" id="pagina-carrinho">
    <h1>Meu Carrinho de Compras</h1>

    <div id="conteudo-carrinho">
        <?php if (empty($itens_carrinho)): ?>
            <div class="carrinho-vazio">
                <h2>Seu carrinho está vazio.</h2>
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn-login" style="margin-top: 20px;">Ver cardápio</a>
            </div>
        <?php else: ?>
            <table class="carrinho-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço Unitário</th>
                        <th style="text-align: center;">Quantidade</th>
                        <th style="text-align: right;">Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens_carrinho as $item): ?>
                        <?php
                            $preco_item_personalizado = $item['preco_produto'];
                            $personalizacao = json_decode($item['personalizacao'], true);
                            if (!empty($personalizacao)) {
                                foreach ($personalizacao as $opcional) {
                                    $preco_item_personalizado += $opcional['preco'];
                                }
                            }
                            $subtotal = $preco_item_personalizado * $item['quantidade'];
                            $total_carrinho += $subtotal;
                        ?>
                        <tr data-carrinho-id="<?php echo $item['carrinho_id']; ?>">
                            <td>
                                <div class="produto-info-carrinho">
                                    <img src="<?php echo BASE_URL . '/assets/images/' . htmlspecialchars($item['imagem']); ?>" width="80" alt="<?php echo htmlspecialchars($item['nome_produto']); ?>">
                                    <div>
                                        <span><?php echo htmlspecialchars($item['nome_produto']); ?></span>
                                        <?php if (!empty($personalizacao)): ?>
                                            <div class="personalizacao-detalhes">
                                                <?php foreach($personalizacao as $opcional): ?>
                                                    + <?php echo htmlspecialchars($opcional['nome']); ?><br>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>R$ <?php echo number_format($preco_item_personalizado, 2, ',', '.'); ?></td>
                            <td align="center">
                                <div class="quantity-stepper">
                                    <button class="quantity-btn" data-action="decrease" data-carrinho-id="<?php echo $item['carrinho_id']; ?>">-</button>
                                    <span class="quantity-value"><?php echo $item['quantidade']; ?></span>
                                    <button class="quantity-btn" data-action="increase" data-carrinho-id="<?php echo $item['carrinho_id']; ?>">+</button>
                                </div>
                            </td>
                            <td align="right">
                                <span class="subtotal-valor">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                            </td>
                            <td><a href="#" class="action-delete-item" data-carrinho-id="<?php echo $item['carrinho_id']; ?>">Remover</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-carrinho">Total: <span id="valor-total-carrinho">R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></span></div>
            
            <?php if ($loja_aberta): ?>
                <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn-finalizar">Finalizar Compra</a>
            <?php else: ?>
                <div style="text-align: right; margin-top: 10px;">
                    <div style='background-color: #f8d7da; color: #721c24; padding: 15px; text-align: center; border-radius: 8px; display: inline-block; font-weight: 600; margin-bottom: 10px;'>
                        A loja está fechada e não aceita pedidos no momento.
                    </div>
                    <a class="btn-finalizar" style="background-color: #ccc; cursor: not-allowed; opacity: 0.6; float: right;">Finalizar Compra</a>
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
</div>

<?php 
require_once __DIR__ . '/../app/templates/footer.php';
?>