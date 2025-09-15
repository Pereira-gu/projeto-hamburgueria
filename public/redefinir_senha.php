<?php
require_once __DIR__ . '/../app/templates/header.php';
require_once __DIR__ . '/../app/includes/conexao.php';

$token = $_GET['token'] ?? null;
$token_valido = false;
$mensagem_erro = '';

if ($token) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires >= ?");
        $stmt->execute([$token, time()]);
        if ($stmt->fetch()) {
            $token_valido = true;
        } else {
            $mensagem_erro = "Token inválido ou expirado. Por favor, solicite a recuperação novamente.";
        }
    } catch (PDOException $e) {
        $mensagem_erro = "Ocorreu um erro no servidor.";
    }
} else {
    $mensagem_erro = "Nenhum token fornecido.";
}
?>

<div class="container page-content">
    <div class="form-box" style="max-width: 450px; margin: 40px auto;">
        <h2>Criar Nova Senha</h2>
        
        <?php if ($token_valido): ?>
            <form action="<?php echo BASE_URL; ?>/actions/processar_redefinicao.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <input type="password" name="nova_senha" placeholder="Digite sua nova senha (mín. 8 caracteres)" required minlength="8">
                <input type="password" name="confirmar_senha" placeholder="Confirme sua nova senha" required>
                
                <button type="submit">Salvar Nova Senha</button>
            </form>
        <?php else: ?>
            <p style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;"><?php echo $mensagem_erro; ?></p>
            <a href="esqueci_senha.php" class="btn-login" style="margin-top:20px;">Tentar Novamente</a>
        <?php endif; ?>

        <?php if(isset($_GET['erro'])): ?>
            <p style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-top: 15px;">As senhas não coincidem. Tente novamente.</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../app/templates/footer.php';
?>