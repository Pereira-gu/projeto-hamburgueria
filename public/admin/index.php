<?php 
// Caminho corrigido para "subir" duas pastas e encontrar a pasta app
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM produtos ORDER BY categoria, nome_produto");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
    error_log("Erro ao buscar produtos: " . $e->getMessage());
}
?>
<style>
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    .admin-table th { background-color: var(--cor-fundo-escuro); color: var(--cor-texto-claro); }
    .admin-actions a { margin-right: 10px; color: #007bff; text-decoration: none; }
    .admin-actions a:hover { text-decoration: underline; }
</style>

<div class="container page-content">
    <h1>Gerenciamento de Produtos</h1>

    <a href="adicionar_produto.php" class="btn-login">Adicionar Novo Produto</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>" width="80"></td>
                    <td><?php echo htmlspecialchars($produto['nome_produto']); ?></td>
                    <td><?php echo htmlspecialchars($produto['categoria']); ?></td>
                    <td>R$ <?php echo number_format($produto['preco_produto'], 2, ',', '.'); ?></td>
                    <td class="admin-actions">
                        <a href="editar_produto.php?id=<?php echo $produto['id']; ?>">Editar</a>
                        <a href="excluir_produto.php?id=<?php echo $produto['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">
                        Nenhum produto cadastrado ainda.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Caminho corrigido para encontrar o footer
require_once __DIR__ . '/../../app/templates/admin_footer.php'; 
?>