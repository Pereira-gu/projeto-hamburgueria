<?php
require_once __DIR__ . '/../app/templates/header.php';
require_once __DIR__ . '/../app/includes/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/cadastro.php?status=login_necessario');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$pedidos = [];

try {
    // Busca os pedidos do usuário e verifica se já foram avaliados
    $sql = "SELECT p.id, p.valor_total, p.data_pedido, p.status, 
                   (SELECT COUNT(*) FROM avaliacoes a WHERE a.pedido_id = p.id) as total_avaliacoes
            FROM pedidos p
            WHERE p.login_id = ? 
            ORDER BY p.data_pedido DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar pedidos do usuário: " . $e->getMessage());
    die("Ocorreu um erro ao carregar seus pedidos.");
}
?>
<style>
    .pedidos-table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .pedidos-table th, .pedidos-table td { border-bottom: 1px solid #ddd; padding: 15px; text-align: left; vertical-align: middle; }
    .pedidos-table th { background-color: var(--cor-fundo-escuro); color: var(--cor-texto-claro); }
    .pedidos-table tr:nth-child(even) { background-color: #f9f9f9; }
    .status-badge { padding: 5px 12px; border-radius: 15px; font-weight: 600; font-size: 0.9rem; color: #fff; text-align: center; }
    .status-pendente { background-color: #ffc107; color: #333; }
    .status-em-preparo { background-color: #17a2b8; }
    .status-saiu-para-entrega { background-color: #007bff; }
    .status-concluido { background-color: #28a745; }
    .status-cancelado { background-color: #dc3545; }
</style>

<div class="container page-content">
    <h1>Meus Pedidos</h1>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'avaliacao_ok'): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 600;">
            Obrigado pela sua avaliação!
        </div>
    <?php endif; ?>

    <?php if (empty($pedidos)): ?>
        <p style="text-align: center; font-size: 1.1rem; margin-top: 30px;">Você ainda não fez nenhum pedido.</p>
    <?php else: ?>
        <table class="pedidos-table">
            <thead>
                <tr>
                    <th>Nº do Pedido</th>
                    <th>Data</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): 
                    $status_class = 'status-' . strtolower(str_replace(' ', '-', $pedido['status']));
                ?>
                    <tr>
                        <td>#<?php echo $pedido['id']; ?></td>
                        <td><?php echo date('d/m/Y \à\s H:i', strtotime($pedido['data_pedido'])); ?></td>
                        <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($pedido['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/actions/pedir_de_novo.php?pedido_id=<?php echo $pedido['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" class="btn-login" style="padding: 5px 10px; font-size: 0.9rem;">Pedir de Novo</a>
                            
                            <?php if ($pedido['status'] == 'Concluído' && $pedido['total_avaliacoes'] == 0): ?>
                                <a href="avaliar_pedido.php?pedido_id=<?php echo $pedido['id']; ?>" class="btn-login" style="padding: 5px 10px; font-size: 0.9rem; background-color: #007bff; margin-left: 10px;">Avaliar</a>
                            <?php elseif ($pedido['status'] == 'Concluído'): ?>
                                <span style="margin-left: 10px; color: #28a745; font-weight: bold;">✓ Avaliado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php 
require_once __DIR__ . '/../app/templates/footer.php'; 
?>