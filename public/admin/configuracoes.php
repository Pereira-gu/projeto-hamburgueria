<?php
require_once __DIR__ . '/../../app/templates/admin_header.php';
require_once __DIR__ . '/../../app/includes/conexao.php';

// Busca as configurações atuais no banco de dados
try {
    $stmt = $pdo->query("SELECT chave, valor FROM configuracoes WHERE chave IN ('HORARIO_ABERTURA', 'HORARIO_FECHAMENTO')");
    $configs_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $horario_abertura = $configs_raw['HORARIO_ABERTURA'] ?? '09:00';
    $horario_fechamento = $configs_raw['HORARIO_FECHAMENTO'] ?? '22:00';

} catch (PDOException $e) {
    die("Erro ao carregar configurações: " . $e->getMessage());
}
?>

<div class="container page-content">
    <h1>Configurações da Loja</h1>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 600;">
            Horários atualizados com sucesso!
        </div>
    <?php endif; ?>

    <div class="admin-form-box" style="max-width: 600px; margin: 20px auto;">
        <h2>Horário de Funcionamento</h2>
        <p style="margin-bottom: 20px;">Defina o intervalo em que a loja aceitará novos pedidos.</p>

        <form action="processar_configuracoes.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="horario_abertura">Abre às:</label>
            <input type="time" id="horario_abertura" name="horario_abertura" value="<?php echo htmlspecialchars($horario_abertura); ?>" required>
            
            <label for="horario_fechamento">Fecha às:</label>
            <input type="time" id="horario_fechamento" name="horario_fechamento" value="<?php echo htmlspecialchars($horario_fechamento); ?>" required>

            <button type="submit">Salvar Horários</button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../../app/templates/admin_footer.php';
?>