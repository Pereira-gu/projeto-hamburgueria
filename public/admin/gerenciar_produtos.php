<?php
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

<div class="container page-content">
    <h1>Gerenciamento de Produtos</h1>

    <a href="adicionar_produto.php" class="btn-login" style="margin-bottom: 20px; display: inline-block;">Adicionar Novo Produto</a>

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
require_once __DIR__ . '/../../app/templates/admin_footer.php';
?>