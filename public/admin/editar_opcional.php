<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

// Pega o ID do opcional pela URL
$id_opcional = $_GET['id'] ?? null;
if (!$id_opcional) {
    header('Location: ' . BASE_URL . '/admin/gerenciar_opcionais.php');
    exit();
}

// Busca os dados do opcional no banco de dados
try {
    $stmt = $pdo->prepare("SELECT * FROM opcionais WHERE id = ?");
    $stmt->execute([$id_opcional]);
    $opcional = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$opcional) {
        // Se não encontrar, volta para a página principal
        header('Location: ' . BASE_URL . '/admin/gerenciar_opcionais.php');
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao buscar opcional: " . $e->getMessage());
}
?>

<div class="admin-form-box" style="max-width: 600px; margin: 40px auto;">
    <h2>Editando Opcional</h2>
    <form action="processar_edicao_opcional.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="id" value="<?php echo $opcional['id']; ?>">

        <label for="grupo">Grupo</label>
        <input type="text" name="grupo" id="grupo" value="<?php echo htmlspecialchars($opcional['grupo']); ?>" required>

        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($opcional['nome']); ?>" required>
        
        <label for="preco">Preço</label>
        <input type="number" step="0.01" name="preco" id="preco" value="<?php echo htmlspecialchars($opcional['preco']); ?>" required>
        
        <button type="submit">Salvar Alterações</button>
    </form>
</div>

<?php
require_once __DIR__ . '/../../app/templates/admin_footer.php';
?>