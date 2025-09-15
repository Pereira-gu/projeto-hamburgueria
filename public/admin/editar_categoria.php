<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

$id_categoria = $_GET['id'] ?? null;
if (!$id_categoria) {
    header('Location: ' . BASE_URL . '/admin/gerenciar_categorias.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$id_categoria]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$categoria) {
        header('Location: ' . BASE_URL . '/admin/gerenciar_categorias.php');
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao buscar a categoria: " . $e->getMessage());
}
?>

<div class="admin-form-box" style="max-width: 600px; margin: 40px auto;">
    <h2>Editando Categoria</h2>
    <form action="processar_edicao_categoria.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">

        <label for="nome">Nome da Categoria</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
        
        <button type="submit">Salvar Alterações</button>
    </form>
</div>

<?php
require_once __DIR__ . '/../../app/templates/admin_footer.php';
?>