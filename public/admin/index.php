<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

try {
    // Card 1: Pedidos Pendentes
    $stmt_pendentes = $pdo->prepare("SELECT COUNT(id) FROM pedidos WHERE status = 'Pendente'");
    $stmt_pendentes->execute();
    $total_pendentes = $stmt_pendentes->fetchColumn();

    // Card 2: Faturamento do Mês
    $stmt_faturamento = $pdo->prepare("SELECT SUM(valor_total) FROM pedidos WHERE MONTH(data_pedido) = MONTH(CURDATE()) AND YEAR(data_pedido) = YEAR(CURDATE()) AND status = 'Concluído'");
    $stmt_faturamento->execute();
    $faturamento_mes = $stmt_faturamento->fetchColumn();

    // Card 3: Total de Clientes
    $stmt_clientes = $pdo->prepare("SELECT COUNT(id) FROM login WHERE is_admin = 0");
    $stmt_clientes->execute();
    $total_clientes = $stmt_clientes->fetchColumn();

    // Dados para o Gráfico de Vendas dos Últimos 7 Dias
    $stmt_grafico = $pdo->prepare("
        SELECT DATE(data_pedido) as dia, SUM(valor_total) as total 
        FROM pedidos 
        WHERE data_pedido >= CURDATE() - INTERVAL 6 DAY AND status = 'Concluído'
        GROUP BY dia 
        ORDER BY dia ASC
    ");
    $stmt_grafico->execute();
    $vendas_semana = $stmt_grafico->fetchAll(PDO::FETCH_ASSOC);

    // Formata os dados para o JavaScript
    $labels_grafico = [];
    $dados_grafico = [];
    $dias_semana = [];
    for ($i = 6; $i >= 0; $i--) {
        $dias_semana[date('Y-m-d', strtotime("-$i days"))] = 0;
    }
    foreach ($vendas_semana as $venda) {
        $dias_semana[$venda['dia']] = $venda['total'];
    }
    foreach ($dias_semana as $dia => $total) {
        $labels_grafico[] = date('d/m', strtotime($dia));
        $dados_grafico[] = $total;
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados do dashboard: " . $e->getMessage());
}
?>
<style>
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .admin-table th,
    .admin-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .admin-table th {
        background-color: var(--cor-fundo-escuro);
        color: var(--cor-texto-claro);
    }

    .admin-actions a {
        margin-right: 10px;
        color: #007bff;
        text-decoration: none;
    }

    .admin-actions a:hover {
        text-decoration: underline;
    }
</style>

<div class="container page-content">
    <h1>Dashboard</h1>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Pedidos Pendentes</h3>
            <p class="dashboard-card-valor"><?php echo $total_pendentes; ?></p>
        </div>
        <div class="dashboard-card">
            <h3>Faturamento do Mês</h3>
            <p class="dashboard-card-valor">R$ <?php echo number_format($faturamento_mes ?? 0, 2, ',', '.'); ?></p>
        </div>
        <div class="dashboard-card">
            <h3>Total de Clientes</h3>
            <p class="dashboard-card-valor"><?php echo $total_clientes; ?></p>
        </div>
    </div>

    <div class="dashboard-grafico-container">
        <h3>Vendas dos Últimos 7 Dias (Pedidos Concluídos)</h3>
        <canvas id="graficoVendas"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('graficoVendas').getContext('2d');

        new Chart(ctx, {
            type: 'line', // Tipo do gráfico
            data: {
                labels: <?php echo json_encode($labels_grafico); ?>, // Dias (ex: '12/09', '13/09')
                datasets: [{
                    label: 'Faturamento Diário',
                    data: <?php echo json_encode($dados_grafico); ?>, // Valores (ex: 150.50, 230.00)
                    backgroundColor: 'rgba(255, 160, 0, 0.2)',
                    borderColor: 'rgba(255, 160, 0, 1)',
                    borderWidth: 3,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>

<?php
require_once __DIR__ . '/../../app/templates/admin_footer.php';
?>