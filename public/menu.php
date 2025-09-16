<?php
require_once __DIR__ . '/../app/includes/conexao.php';

// ... (código PHP existente no topo do arquivo permanece o mesmo) ...
// --- LÓGICA DO HORÁRIO DE FUNCIONAMENTO ---
$loja_aberta = false;
try {
    $stmt_config = $pdo->query("SELECT * FROM configuracoes WHERE chave IN ('HORARIO_ABERTURA', 'HORARIO_FECHAMENTO')");
    $configs = $stmt_config->fetchAll(PDO::FETCH_KEY_PAIR);

    $abertura = $configs['HORARIO_ABERTURA'] ?? '00:00';
    $fechamento = $configs['HORARIO_FECHAMENTO'] ?? '23:59';

    date_default_timezone_set('America/Sao_Paulo');
    $hora_atual = date('H:i');

    if ($fechamento < $abertura) {
        // A loja está aberta se a hora atual for MAIOR que a abertura OU MENOR que o fechamento.
        if ($hora_atual >= $abertura || $hora_atual < $fechamento) {
            $loja_aberta = true;
        }
    } else {
        // Caso contrário, o funcionamento é no mesmo dia. A lógica original funciona.
        if ($hora_atual >= $abertura && $hora_atual < $fechamento) {
            $loja_aberta = true;
        }
    }
} catch (PDOException $e) {
    $loja_aberta = true;
    error_log("Erro ao buscar configurações de horário: " . $e->getMessage());
}
// --- FIM DA LÓGICA DO HORÁRIO ---

try {
    // --- QUERY ATUALIZADA PARA INCLUIR AVALIAÇÕES ---
    $sql = "SELECT p.*, AVG(a.nota) as media_avaliacoes, COUNT(a.id) as total_avaliacoes FROM produtos p LEFT JOIN avaliacoes a ON p.id = a.produto_id WHERE p.disponivel = 1 GROUP BY p.id ORDER BY p.categoria, p.nome_produto";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cardapio_dinamico = [];
    foreach ($produtos as $produto) {
        $cardapio_dinamico[$produto['categoria']][] = $produto;
    }

    $ordem_desejada = ['Burgers', 'Acompanhamentos', 'Bebidas', 'Sobremesas'];
    $cardapio_ordenado = [];
    foreach ($ordem_desejada as $categoria) {
        if (array_key_exists($categoria, $cardapio_dinamico)) {
            $cardapio_ordenado[$categoria] = $cardapio_dinamico[$categoria];
            unset($cardapio_dinamico[$categoria]);
        }
    }
    foreach ($cardapio_dinamico as $categoria => $itens) {
        $cardapio_ordenado[$categoria] = $itens;
    }

    if (!$loja_aberta) {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 30px; font-weight: 600;'>";
        echo "Estamos fechados no momento. Nosso horário de funcionamento é das " . htmlspecialchars($abertura) . " às " . htmlspecialchars($fechamento) . ".";
        echo "</div>";
    }

    if (!empty($cardapio_ordenado)) {
        foreach ($cardapio_ordenado as $categoria => $itens) {
            echo "<section class='categoria'>";
            echo "<h2>" . htmlspecialchars($categoria) . "</h2>";
            echo "<div class='itens-grid'>";

            foreach ($itens as $item) {
                echo "<div class='item' data-categoria='" . htmlspecialchars($item['categoria']) . "'>";
                echo "<a href='" . BASE_URL . "/produto_detalhe.php?id=" . $item['id'] . "'><img src='" . BASE_URL . "/assets/images/" . htmlspecialchars($item['imagem']) . "' alt='" . htmlspecialchars($item['nome_produto']) . "'></a>";
                echo "<div class='item-content'>";
                echo "<a href='" . BASE_URL . "/produto_detalhe.php?id=" . $item['id'] . "' style='text-decoration: none; color: inherit;'><h3>" . htmlspecialchars($item['nome_produto']) . "</h3></a>";

                if ($item['total_avaliacoes'] > 0) {
                    echo "<div class='avaliacao-estrelas-card'>";
                    for ($i = 1; $i <= 5; $i++) {
                        echo "<span class='estrela " . ($i <= round($item['media_avaliacoes']) ? "preenchida" : "") . "'>★</span>";
                    }
                    echo "<span class='total-avaliacoes'>(" . $item['total_avaliacoes'] . ")</span>";
                    echo "</div>";
                }

                echo "<p>" . htmlspecialchars($item['descricao_produto']) . "</p>";
                echo "<div class='item-footer'>";
                echo "<span class='preco'>R$ " . number_format($item['preco_produto'], 2, ',', '.') . "</span>";

                if ($loja_aberta) {
                    echo "<form action='" . BASE_URL . "/actions/adicionar_carrinho.php' method='POST'>";
                    echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
                    echo "<input type='hidden' name='produto_id' value='" . $item['id'] . "'>";
                    // Alteração aqui: adicionamos a classe 'js-add-to-cart'
                    echo "<button type='submit' class='btn-adicionar js-add-to-cart'>Adicionar</button>";
                    echo "</form>";
                } else {
                    echo "<button type='button' class='btn-adicionar' disabled style='background-color: #ccc; cursor: not-allowed;'>Fechado</button>";
                }

                echo "</div></div></div>";
            }

            echo "</div></section>";
        }
    } else {
        echo "<p>Nenhum produto cadastrado no momento.</p>";
    }
} catch (PDOException $e) {
    error_log("Erro no banco de dados: " . $e->getMessage());
    echo "<p>Ocorreu um erro ao carregar o cardápio. Tente novamente mais tarde.</p>";
}
