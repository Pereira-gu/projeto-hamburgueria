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

$produto_id = $_GET['id'] ?? null;
if (!$produto_id || !is_numeric($produto_id)) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

try {
    // --- QUERY ATUALIZADA PARA BUSCAR DADOS DO PRODUTO E AVALIAÇÕES ---
    $stmt = $pdo->prepare("
        SELECT p.*, AVG(a.nota) as media_avaliacoes, COUNT(a.id) as total_avaliacoes
        FROM produtos p
        LEFT JOIN avaliacoes a ON p.id = a.produto_id
        WHERE p.id = ? AND p.disponivel = 1
        GROUP BY p.id
    ");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }
    
    // Lógica para buscar os opcionais (existente)
    $sql_opcionais = "SELECT o.id, o.grupo, o.nome, o.preco FROM opcionais o JOIN produto_opcionais po ON o.id = po.opcional_id WHERE po.produto_id = ? ORDER BY o.grupo, o.nome";
    $stmt_opcionais = $pdo->prepare($sql_opcionais);
    $stmt_opcionais->execute([$produto_id]);
    $opcionais_disponiveis = $stmt_opcionais->fetchAll(PDO::FETCH_ASSOC);
    $opcionais_agrupados = [];
    foreach ($opcionais_disponiveis as $opcional) {
        $opcionais_agrupados[$opcional['grupo']][] = $opcional;
    }

} catch (PDOException $e) {
    error_log("Erro ao buscar detalhes do produto: " . $e->getMessage());
    die("Ops! Tivemos um problema ao carregar o produto. Por favor, tente novamente mais tarde.");
}
?>
<style>
    /* Estilos da personalização (existente) */
    .opcionais-container { margin-top: 25px; border-top: 1px solid #eee; padding-top: 25px; }
    .opcional-grupo { margin-bottom: 20px; }
    .opcional-grupo h3 { font-size: 1.2rem; margin-bottom: 10px; }
    .opcional-item label { display: flex; justify-content: space-between; padding: 10px; border-radius: 5px; cursor: pointer; transition: background-color 0.2s ease; }
    .opcional-item label:hover { background-color: #f5f5f5; }
    .opcional-item input { margin-right: 10px; }
    .opcional-preco { font-weight: 600; color: #007bff; }
    #preco-total { transition: color 0.3s ease, transform 0.3s ease; }
</style>

<div class="container page-content">
    <form action="<?php echo BASE_URL; ?>/actions/adicionar_carrinho.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">

        <div class="detalhe-grid">
            <div class="detalhe-imagem">
                <img src="<?php echo BASE_URL . '/assets/images/' . htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>">
            </div>
            <div class="detalhe-info">
                <h1><?php echo htmlspecialchars($produto['nome_produto']); ?></h1>

                <?php if ($produto['total_avaliacoes'] > 0): ?>
                    <div class="avaliacao-estrelas-detalhe">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="estrela <?php echo ($i <= round($produto['media_avaliacoes'])) ? 'preenchida' : ''; ?>">★</span>
                        <?php endfor; ?>
                        <span class="total-avaliacoes-detalhe">
                            <?php echo number_format($produto['media_avaliacoes'], 1, '.'); ?> de 5 estrelas (<?php echo $produto['total_avaliacoes']; ?> avaliações)
                        </span>
                    </div>
                <?php endif; ?>

                <p class="detalhe-descricao"><?php echo htmlspecialchars($produto['descricao_produto']); ?></p>
                
                <?php if (!empty($opcionais_agrupados)): ?>
                <div class="opcionais-container">
                    <?php foreach($opcionais_agrupados as $grupo => $opcionais): ?>
                        <div class="opcional-grupo">
                            <h3><?php echo htmlspecialchars($grupo); ?></h3>
                            <?php foreach($opcionais as $opcional): 
                                $tipo_input = (in_array($grupo, ['Adicionais', 'Remover'])) ? 'checkbox' : 'radio';
                            ?>
                                <div class="opcional-item">
                                    <label>
                                        <span>
                                            <input type="<?php echo $tipo_input; ?>" name="opcionais[<?php echo htmlspecialchars($grupo); ?>][]" value="<?php echo $opcional['id']; ?>" data-preco="<?php echo $opcional['preco']; ?>">
                                            <?php echo htmlspecialchars($opcional['nome']); ?>
                                        </span>
                                        <?php if ($opcional['preco'] > 0): ?>
                                            <span class="opcional-preco">+ R$ <?php echo number_format($opcional['preco'], 2, ',', '.'); ?></span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="detalhe-preco" id="preco-total">R$ <?php echo number_format($produto['preco_produto'], 2, ',', '.'); ?></div>
                
                <?php if ($loja_aberta): ?>
                    <button type="submit" class="btn-login detalhe-add-carrinho js-add-to-cart">Adicionar ao Carrinho</button>
                <?php else: ?>
                    <div style='background-color: #f8d7da; color: #721c24; padding: 15px; text-align: center; border-radius: 8px; margin-top: 20px; font-weight: 600;'>
                        Estamos fechados no momento.
                    </div>
                    <button type="button" class="btn-login detalhe-add-carrinho" disabled style="background-color: #ccc; cursor: not-allowed; margin-top: 10px;">Loja Fechada</button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const precoBase = <?php echo $produto['preco_produto']; ?>;
    const elementoPrecoTotal = document.getElementById('preco-total');
    const inputsOpcionais = document.querySelectorAll('.opcional-item input');

    function atualizarPreco() {
        let precoAdicional = 0;
        inputsOpcionais.forEach(input => {
            if (input.checked) {
                precoAdicional += parseFloat(input.dataset.preco);
            }
        });

        const precoFinal = precoBase + precoAdicional;
        elementoPrecoTotal.textContent = 'R$ ' + precoFinal.toFixed(2).replace('.', ',');
        
        elementoPrecoTotal.style.transform = 'scale(1.05)';
        setTimeout(() => {
            elementoPrecoTotal.style.transform = 'scale(1)';
        }, 150);
    }

    inputsOpcionais.forEach(input => {
        input.addEventListener('change', atualizarPreco);
    });
});
</script>

<?php require_once __DIR__ . '/../app/templates/footer.php'; ?>