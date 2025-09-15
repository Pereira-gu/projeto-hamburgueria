<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

$statuses_pedido = ['Pendente', 'Em Preparo', 'Saiu para Entrega', 'Concluído', 'Cancelado'];

try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.valor_total, p.data_pedido, p.status, l.nome as cliente_nome
        FROM pedidos p
        JOIN login l ON p.login_id = l.id
        ORDER BY p.data_pedido DESC
    ");
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pedidos = [];
    error_log("Erro ao buscar pedidos: " . $e->getMessage());
}
?>
<style>
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .admin-table th, .admin-table td { border-bottom: 1px solid #ddd; padding: 12px; text-align: left; }
    .admin-table th { background-color: var(--cor-fundo-escuro); color: var(--cor-texto-claro); }
    .admin-table tr:nth-child(even) { background-color: #f9f9f9; }
    .admin-table a { color: #007bff; text-decoration: none; font-weight: 600; }
    .admin-table a:hover { text-decoration: underline; }
    .status-select { padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-family: 'Poppins', sans-serif; }
    .btn-atualizar { background-color: #007bff; color: white; border: none; padding: 6px 12px; font-size: 0.9rem; margin-left: 10px; cursor: pointer; border-radius: 4px; }
</style>

<div class="container page-content">
    <h1>Gerenciamento de Pedidos</h1>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Valor Total</th>
                <th>Status Atual</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pedidos)): ?>
                <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td>#<?php echo $pedido['id']; ?></td>
                    <td><?php echo htmlspecialchars($pedido['cliente_nome']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                    <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                    <td>
                        <form action="atualizar_status_pedido.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <?php foreach ($statuses_pedido as $status): ?>
                                    <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($pedido['status'] == $status) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a href="detalhes_pedido.php?id=<?php echo $pedido['id']; ?>">Ver Detalhes</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        Nenhum pedido encontrado.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
require_once __DIR__ . '/../../app/templates/admin_footer.php'; 
?>