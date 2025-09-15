<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

try {
    $categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro ao carregar categorias.");
}
?>

<div class="container page-content">
    <h1>Gerenciamento de Categorias</h1>
    
    <?php if (isset($_GET['status']) && $_GET['status'] == 'erro_produtos_vinculados'): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 600;">
            <b>Erro:</b> Não é possível excluir esta categoria, pois existem produtos vinculados a ela.
        </div>
    <?php endif; ?>

    <div class="admin-grid">
        <div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nome da Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categorias as $categoria): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                        <td>
                            <a href="editar_categoria.php?id=<?php echo $categoria['id']; ?>">Editar</a> | 
                            <a href="excluir_categoria.php?id=<?php echo $categoria['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" class="action-delete" onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="admin-form-box">
            <h2>Adicionar Nova Categoria</h2>
            <form action="processar_categoria.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <label for="nome">Nome da Categoria</label>
                <input type="text" name="nome" id="nome" required>
                <button type="submit">Adicionar</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../app/templates/admin_footer.php';
?>