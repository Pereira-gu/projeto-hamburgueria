<?php 
require_once __DIR__ . '/../../app/templates/admin_header.php'; 
require_once __DIR__ . '/../../app/includes/conexao.php'; // Inclui a conexão

// Busca as categorias do banco de dados
try {
    $lista_categorias = $pdo->query("SELECT nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar categorias.");
}
?>

<div class="form-box" style="max-width: 600px; margin: 40px auto;">
    <h2>Adicionar Novo Produto</h2>
    
    <form action="processar_produto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <label>Nome do Produto</label>
        <input type="text" name="nome_produto" placeholder="Ex: X-Bacon Supremo" required>
        
        <label>Descrição</label>
        <textarea name="descricao_produto" placeholder="Ex: Pão brioche, hambúrguer de 180g..." required style="width:100%; min-height: 100px; padding:15px; margin-bottom:20px; border:1px solid #ddd; border-radius:5px; font-family: Poppins, sans-serif; font-size:1rem;"></textarea>
        
        <label>Preço</label>
        <input type="number" step="0.01" name="preco_produto" placeholder="Ex: 25.50" required>
        
        <label>Categoria</label>
        <select name="categoria" required style="width: 100%; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; background-color: #fff;">
            <option value="">Selecione uma categoria</option>
            <?php foreach ($lista_categorias as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['nome']); ?>"><?php echo htmlspecialchars($cat['nome']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="imagem" style="display:block; margin-bottom: 10px; text-align:left;">Imagem do Produto:</label>
        <input type="file" name="imagem" id="imagem" required accept="image/png, image/jpeg, image/gif, image/webp">
        
        <button type="submit" style="margin-top: 20px;">Cadastrar Produto</button>
    </form>
</div>

<?php 
require_once __DIR__ . '/../../app/templates/admin_footer.php'; 
?>