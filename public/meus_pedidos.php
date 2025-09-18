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
    .pedidos-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .pedidos-table th,
    .pedidos-table td {
        border-bottom: 1px solid #ddd;
        padding: 15px;
        text-align: left;
        vertical-align: middle;
    }

    .pedidos-table th {
        background-color: var(--cor-fundo-escuro);
        color: var(--cor-texto-claro);
    }

    .pedidos-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #fff;
        text-align: center;
    }

    .status-pendente {
        background-color: #ffc107;
        color: #333;
    }

    .status-em-preparo {
        background-color: #17a2b8;
    }

    .status-saiu-para-entrega {
        background-color: #007bff;
    }

    .status-concluido {
        background-color: #28a745;
    }

    .status-cancelado {
        background-color: #dc3545;
    }
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
                    <tr data-pedido-id="<?php echo $pedido['id']; ?>">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Pega todos os IDs de pedidos visíveis na página
        const pedidoRows = document.querySelectorAll('[data-pedido-id]');
        const pedidoIds = Array.from(pedidoRows).map(row => row.dataset.pedidoId);

        if (pedidoIds.length === 0) return;

        // 2. Função que verifica os status no servidor
        async function verificarStatus() {
            try {
                const response = await fetch('<?php echo BASE_URL; ?>/actions/verificar_status_pedidos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        pedido_ids: pedidoIds
                    })
                });
                const statuses = await response.json();

                // 3. Atualiza a tabela se houver mudanças
                for (const pedidoId in statuses) {
                    const novoStatus = statuses[pedidoId];
                    const row = document.querySelector(`tr[data-pedido-id="${pedidoId}"]`);
                    if (row) {
                        const statusBadge = row.querySelector('.status-badge');
                        const statusAtual = statusBadge.textContent.trim();

                        if (statusAtual !== novoStatus) {
                            // Atualiza o texto e a cor do badge (código existente)
                            statusBadge.textContent = novoStatus;
                            const novaClasse = 'status-' + novoStatus.toLowerCase().replace(/ /g, '-');
                            statusBadge.className = 'status-badge ' + novaClasse;

                            // Efeito visual de destaque (código existente)
                            row.style.backgroundColor = '#fff3cd';
                            setTimeout(() => {
                                row.style.backgroundColor = '';
                            }, 2000);

                            // --- NOVA LÓGICA PARA CRIAR O BOTÃO "AVALIAR" ---
                            if (novoStatus === 'Concluído') {
                                const acoesCell = row.querySelector('td:last-child');
                                // Verifica se o botão "Avaliar" ou a tag "Avaliado" já não existem
                                if (!acoesCell.querySelector('a[href*="avaliar_pedido"]') && !acoesCell.querySelector('span')) {
                                    // Cria o novo botão "Avaliar"
                                    const avaliarBtn = document.createElement('a');
                                    avaliarBtn.href = `<?php echo BASE_URL; ?>/avaliar_pedido.php?pedido_id=${pedidoId}`;
                                    avaliarBtn.className = 'btn-login';
                                    avaliarBtn.style.cssText = 'padding: 5px 10px; font-size: 0.9rem; background-color: #007bff; margin-left: 10px;';
                                    avaliarBtn.textContent = 'Avaliar';
                                    // Adiciona o botão à célula de ações
                                    acoesCell.appendChild(avaliarBtn);
                                }
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Erro ao verificar status:', error);
            }
        }

        // 4. Roda a verificação a cada 20 segundos
        setInterval(verificarStatus, 20000);
    });
</script>