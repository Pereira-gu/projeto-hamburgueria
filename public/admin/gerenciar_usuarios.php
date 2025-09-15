<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

try {
    // Seleciona apenas os usuários que não são administradores (clientes)
    $stmt = $pdo->prepare("SELECT id, nome, email FROM login WHERE is_admin = 0 ORDER BY nome");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $usuarios = [];
    error_log("Erro ao buscar usuários: " . $e->getMessage());
}
?>
<style>
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .admin-table th, .admin-table td { border-bottom: 1px solid #ddd; padding: 12px; text-align: left; }
    .admin-table th { background-color: var(--cor-fundo-escuro); color: var(--cor-texto-claro); }
    .admin-table tr:nth-child(even) { background-color: #f9f9f9; }
</style>

<div class="container page-content">
    <h1>Gerenciamento de Clientes</h1>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID do Cliente</th>
                <th>Nome</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id']; ?></td>
                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px;">
                        Nenhum cliente cadastrado.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
require_once __DIR__ . '/../../app/templates/admin_footer.php'; 
?>