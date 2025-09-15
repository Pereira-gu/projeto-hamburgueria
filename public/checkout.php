<?php
require_once __DIR__ . '/../app/templates/header.php';
require_once __DIR__ . '/../app/includes/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/cadastro.php?status=login_necessario');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$total_carrinho = 0;

try {
    $sql_carrinho = "SELECT p.nome_produto, p.preco_produto, c.quantidade, c.personalizacao FROM carrinho c JOIN produtos p ON c.produtos_id = p.id WHERE c.login_id = ?";
    $stmt_carrinho = $pdo->prepare($sql_carrinho);
    $stmt_carrinho->execute([$id_usuario]);
    $itens_carrinho = $stmt_carrinho->fetchAll(PDO::FETCH_ASSOC);

    if (empty($itens_carrinho)) {
        header('Location: ' . BASE_URL . '/carrinho.php');
        exit();
    }

    $stmt_usuario = $pdo->prepare("SELECT endereco, telefone FROM login WHERE id = ?");
    $stmt_usuario->execute([$id_usuario]);
    $dados_usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar informações: " . $e->getMessage());
}
?>

<div class="container page-content">
    <h1>Finalizar Compra</h1>

    <div class="checkout-grid">
        <div class="form-box checkout-form-box">
            <form id="checkout-form" action="<?php echo BASE_URL; ?>/actions/processar_pedido.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div id="step-1" class="checkout-step active">
                    <div class="checkout-section">
                        <h2>1. Como quer receber?</h2>
                        <div class="option-group">
                            <label class="option-box"><input type="radio" name="tipo_entrega" value="delivery" checked>
                                <div class="option-box-content"><strong>Delivery</strong><span>Receba em casa</span></div>
                            </label>
                            <label class="option-box"><input type="radio" name="tipo_entrega" value="retirada">
                                <div class="option-box-content"><strong>Retirar no Local</strong><span>Busque seu pedido</span></div>
                            </label>
                        </div>
                    </div>
                    <div class="step-navigation">
                        <button type="button" class="btn-next">Avançar</button>
                    </div>
                </div>

                <div id="step-2" class="checkout-step" style="display: none;">
                    <div id="info-delivery" class="checkout-section">
                        <h2>2. Informações de Entrega</h2>
                        <label for="endereco">Endereço Completo</label>
                        <textarea name="endereco_entrega" id="endereco" required placeholder="Ex: Rua das Flores, 123, Bairro..."><?php echo htmlspecialchars($dados_usuario['endereco'] ?? ''); ?></textarea>
                        
                        <label for="telefone">Telefone para Contato</label>
                        <input type="text" name="telefone_contato" id="telefone" required placeholder="(11) 99999-8888" value="<?php echo htmlspecialchars($dados_usuario['telefone'] ?? ''); ?>">
                    </div>
                    <div id="info-retirada" class="checkout-section" style="display: none;">
                        <h2>2. Informações para Retirada</h2>
                        <p>Nosso endereço é: <strong>Rua Fictícia, 100 - Centro</strong>.</p>
                        <label for="telefone_retirada">Seu Telefone para Contato</label>
                        <input type="text" name="telefone_retirada" id="telefone_retirada" placeholder="(11) 99999-8888" value="<?php echo htmlspecialchars($dados_usuario['telefone'] ?? ''); ?>">
                    </div>
                    <div class="step-navigation">
                        <button type="button" class="btn-prev">Voltar</button>
                        <button type="button" class="btn-next">Avançar</button>
                    </div>
                </div>

                <div id="step-3" class="checkout-step" style="display: none;">
                    <div class="checkout-section">
                        <h2>3. Pagamento</h2>
                        <p>O pagamento é realizado na entrega ou retirada.</p>
                        <div class="option-group">
                            <label class="option-box"><input type="radio" name="metodo_pagamento" value="Dinheiro" checked> <div class="option-box-content"><strong>Dinheiro</strong></div></label>
                            <label class="option-box"><input type="radio" name="metodo_pagamento" value="Cartao"> <div class="option-box-content"><strong>Cartão</strong></div></label>
                            <label class="option-box"><input type="radio" name="metodo_pagamento" value="Pix"> <div class="option-box-content"><strong>Pix</strong></div></label>
                        </div>
                        <div id="campo-troco" style="margin-top: 15px;">
                            <label for="troco_para">Precisa de troco?</label>
                            <input type="number" step="0.01" name="troco_para" id="troco_para" placeholder="Deixe em branco se não precisar">
                        </div>
                        <label for="observacoes" style="margin-top: 15px;">Observações do Pedido (opcional)</label>
                        <textarea name="observacoes" id="observacoes" placeholder="Ex: Sem cebola, ponto da carne mal passado..."></textarea>
                    </div>
                    <div class="step-navigation">
                        <button type="button" class="btn-prev">Voltar</button>
                        <button type="submit" class="btn-confirmar-pedido">Confirmar Pedido</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="resumo-pedido">
            <h3>Resumo do Pedido</h3>
            <ul>
            <?php foreach($itens_carrinho as $item): 
                $preco_item = $item['preco_produto'];
                $personalizacao = json_decode($item['personalizacao'], true);
                if (!empty($personalizacao)) {
                    foreach ($personalizacao as $opcional) $preco_item += $opcional['preco'];
                }
                $subtotal_item = $preco_item * $item['quantidade'];
                $total_carrinho += $subtotal_item;
            ?>
                <li>
                    <span><?php echo $item['quantidade']; ?>x <?php echo htmlspecialchars($item['nome_produto']); ?></span>
                    <span>R$ <?php echo number_format($subtotal_item, 2, ',', '.'); ?></span>
                </li>
            <?php endforeach; ?>
            </ul>
            <hr>
            <div class="total-resumo"><strong>Total:</strong> <strong>R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></strong></div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const steps = document.querySelectorAll('.checkout-step');
    const form = document.getElementById('checkout-form');

    function showStep(stepNumber) {
        steps.forEach(step => step.style.display = 'none');
        document.getElementById(`step-${stepNumber}`).style.display = 'block';
        currentStep = stepNumber;
    }

    form.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-next')) {
            // Validação antes de avançar
            if (currentStep === 2) {
                const isDelivery = document.querySelector('input[name="tipo_entrega"]:checked').value === 'delivery';
                if (isDelivery) {
                    const endereco = document.getElementById('endereco').value.trim();
                    const telefone = document.getElementById('telefone').value.trim();
                    if (endereco === '' || telefone === '') {
                        alert('Por favor, preencha seu endereço e telefone para continuar.');
                        return; // Impede o avanço
                    }
                }
            }
            if (currentStep < steps.length) {
                showStep(currentStep + 1);
            }
        } else if (e.target.classList.contains('btn-prev')) {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        }
    });

    // --- LÓGICA PARA ALTERNAR ENTREGA E RETIRADA ---
    const radiosEntrega = document.querySelectorAll('input[name="tipo_entrega"]');
    const infoDelivery = document.getElementById('info-delivery');
    const infoRetirada = document.getElementById('info-retirada');

    function toggleCamposEntrega() {
        if (document.querySelector('input[name="tipo_entrega"]:checked').value === 'retirada') {
            infoDelivery.style.display = 'none';
            infoRetirada.style.display = 'block';
        } else {
            infoDelivery.style.display = 'block';
            infoRetirada.style.display = 'none';
        }
    }
    radiosEntrega.forEach(radio => radio.addEventListener('change', toggleCamposEntrega));
    
    // --- LÓGICA PARA O CAMPO DE TROCO ---
    const radiosPagamento = document.querySelectorAll('input[name="metodo_pagamento"]');
    const campoTroco = document.getElementById('campo-troco');

    function toggleCampoTroco() {
        if (document.querySelector('input[name="metodo_pagamento"]:checked').value === 'Dinheiro') {
            campoTroco.style.display = 'block';
        } else {
            campoTroco.style.display = 'none';
        }
    }
    radiosPagamento.forEach(radio => radio.addEventListener('change', toggleCampoTroco));
    toggleCampoTroco();
});
</script>

<?php require_once __DIR__ . '/../app/templates/footer.php'; ?>