<?php 
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

$id_produto = $_GET['id'] ?? null;
if (!$id_produto) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}

try {
    // Busca o produto
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id_produto]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$produto) {
        header("Location: " . BASE_URL . "/admin/index.php");
        exit();
    }

    // Busca todas as categorias
    $lista_categorias = $pdo->query("SELECT nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

    // Busca todos os opcionais
    $todos_opcionais = $pdo->query("SELECT * FROM opcionais ORDER BY grupo, nome")->fetchAll(PDO::FETCH_ASSOC);

    // Busca os IDs dos opcionais que JÁ ESTÃO vinculados a este produto
    $stmt_opcionais_produto = $pdo->prepare("SELECT opcional_id FROM produto_opcionais WHERE produto_id = ?");
    $stmt_opcionais_produto->execute([$id_produto]);
    $opcionais_atuais = $stmt_opcionais_produto->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>

<div class="admin-form-box" style="max-width: 800px; margin: 40px auto;">
    <h2>Editando: <?php echo htmlspecialchars($produto['nome_produto']); ?></h2>
    
    <form action="processar_edicao.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">

        <label>Nome do Produto</label>
        <input type="text" name="nome_produto" value="<?php echo htmlspecialchars($produto['nome_produto']); ?>" required>
        
        <label>Descrição</label>
        <textarea name="descricao_produto" required><?php echo htmlspecialchars($produto['descricao_produto']); ?></textarea>
        
        <label>Preço</label>
        <input type="number" step="0.01" name="preco_produto" value="<?php echo htmlspecialchars($produto['preco_produto']); ?>" required>
        
        <label>Categoria</label>
        <select name="categoria" required>
            <?php foreach ($lista_categorias as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['nome']); ?>" <?php echo ($produto['categoria'] == $cat['nome']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <div class="opcionais-container-admin">
            <h3>Vincular Opcionais ao Produto</h3>
            <div class="opcionais-grid-admin">
                <?php 
                // Agrupa todos os opcionais por 'grupo' em um array
                $opcionais_agrupados = [];
                foreach($todos_opcionais as $opcional) {
                    $opcionais_agrupados[$opcional['grupo']][] = $opcional;
                }

                // Itera sobre os grupos
                foreach ($opcionais_agrupados as $nome_grupo => $opcionais_do_grupo):
                ?>
                    <div class="opcionais-grupo-admin">
                        <h4><?php echo htmlspecialchars($nome_grupo); ?></h4>
                        <?php 
                        // Itera sobre os opcionais dentro do grupo
                        foreach ($opcionais_do_grupo as $opcional):
                            $checked = in_array($opcional['id'], $opcionais_atuais) ? 'checked' : '';
                        ?>
                            <div class="opcional-item-admin">
                                <label>
                                    <input type="checkbox" name="opcionais[]" value="<?php echo $opcional['id']; ?>" <?php echo $checked; ?>>
                                    <?php echo htmlspecialchars($opcional['nome']); ?>
                                    <span>(+ R$ <?php echo number_format($opcional['preco'], 2, ',', '.'); ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <label style="margin-top: 20px;">Imagem Atual</label>
        <div><img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($produto['imagem']); ?>" width="100" style="border-radius: 5px;"></div>
        
        <label for="imagem">Trocar Imagem (opcional):</label>
        <input type="file" name="imagem" id="imagem" accept="image/png, image/jpeg, image/gif, image/webp">
        
        <button type="submit" style="margin-top: 20px;">Salvar Alterações</button>
    </form>
</div>

<?php 
require_once __DIR__ . '/../../app/templates/admin_footer.php'; 
?>