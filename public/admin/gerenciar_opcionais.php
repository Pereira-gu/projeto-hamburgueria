<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

try {
    $opcionais = $pdo->query("SELECT * FROM opcionais ORDER BY grupo, nome")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar opcionais: " . $e->getMessage());
}
?>

<div class="container page-content">
    <h1>Gerenciamento de Opcionais</h1>

    <div class="admin-grid">
        <div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Grupo</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opcionais as $opcional): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($opcional['grupo']); ?></td>
                        <td><?php echo htmlspecialchars($opcional['nome']); ?></td>
                        <td>R$ <?php echo number_format($opcional['preco'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="editar_opcional.php?id=<?php echo $opcional['id']; ?>">Editar</a> | 
                            <a href="excluir_opcional.php?id=<?php echo $opcional['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" class="action-delete" onclick="return confirm('Tem certeza que deseja excluir este opcional? Ele será desvinculado de todos os produtos.')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-form-box">
            <h2>Adicionar Novo Opcional</h2>
            <form action="processar_opcional.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <label for="grupo">Grupo (Ex: Adicionais, Ponto da Carne)</label>
                <input type="text" name="grupo" id="grupo" required>

                <label for="nome">Nome do Opcional (Ex: Bacon Extra)</label>
                <input type="text" name="nome" id="nome" required>
                
                <label for="preco">Preço (deixe 0.00 se não houver custo)</label>
                <input type="number" step="0.01" name="preco" id="preco" value="0.00" required>
                
                <button type="submit">Adicionar Opcional</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../app/templates/admin_footer.php';
?>