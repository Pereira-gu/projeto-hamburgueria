<?php
require_once __DIR__ . '/../app/templates/header.php';
?>

<div class="container page-content">
    <div class="form-box" style="max-width: 450px; margin: 40px auto;">
        <h2>Recuperar Senha</h2>
        <p style="margin-bottom: 20px;">Digite seu e-mail e enviaremos um link para você criar uma nova senha.</p>
        
        <?php if(isset($_GET['status']) && $_GET['status'] == 'email_enviado'): ?>
            <p style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px;">Se o e-mail existir em nosso sistema, um link de recuperação foi enviado!</p>
        <?php endif; ?>
        <?php if(isset($_GET['erro'])): ?>
            <p style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">Ocorreu um erro. Tente novamente.</p>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/actions/solicitar_recuperacao.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="email" name="email" placeholder="Digite seu e-mail" required>
            <button type="submit">Enviar Link de Recuperação</button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../app/templates/footer.php';
?>