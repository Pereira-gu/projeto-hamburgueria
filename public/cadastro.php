<?php 
require_once __DIR__ . '/../app/templates/header.php';

$mensagem_erro = '';
$mensagem_sucesso = '';

if (isset($_GET['erro'])) {
    $codigo_erro = $_GET['erro'];
    switch ($codigo_erro) {
        case 'campos_vazios': $mensagem_erro = 'Por favor, preencha todos os campos do cadastro.'; break;
        case 'campos_vazios_login': $mensagem_erro = 'Por favor, preencha o e-mail e a senha para entrar.'; break;
        case 'senhas_nao_coincidem': $mensagem_erro = 'As senhas não coincidem. Tente novamente.'; break;
        case 'senha_curta': $mensagem_erro = 'A senha deve ter no mínimo 8 caracteres.'; break;
        case 'email_invalido': $mensagem_erro = 'O formato do e-mail é inválido.'; break;
        case 'email_existente': $mensagem_erro = 'Este e-mail já está cadastrado. Tente fazer login.'; break;
        case 'login_invalido': $mensagem_erro = 'E-mail ou senha incorretos.'; break;
        case 'db_erro': $mensagem_erro = 'Ocorreu um erro no sistema. Tente novamente mais tarde.'; break;
    }
}

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sucesso') {
        $mensagem_sucesso = 'Cadastro realizado com sucesso! Agora você pode fazer o login.';
    }
    if ($_GET['status'] == 'senha_redefinida') {
        $mensagem_sucesso = 'Senha alterada com sucesso! Você já pode entrar com sua nova senha.';
    }
}
?>
<style>
    .mensagem-feedback { padding: 15px; margin: 20px auto; border-radius: 5px; max-width: 450px; text-align: center; font-weight: 600; }
    .mensagem-erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .mensagem-sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .form-switcher { margin-top: 25px; font-size: 0.95rem; color: #555; }
    .form-switcher a { color: var(--cor-primaria); font-weight: 600; text-decoration: none; cursor: pointer; }
    .form-switcher a:hover { text-decoration: underline; }
</style>

<div class="container">
    
    <?php if (!empty($mensagem_erro)): ?>
        <div class="mensagem-feedback mensagem-erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
    <?php endif; ?>
    <?php if (!empty($mensagem_sucesso)): ?>
        <div class="mensagem-feedback mensagem-sucesso"><?php echo htmlspecialchars($mensagem_sucesso); ?></div>
    <?php endif; ?>

    <div class="form-container" style="display: flex; justify-content: center; padding: 0 20px 40px;">
        
        <div class="form-box" id="login-form-container">
            <h2>Login</h2>
            <form action="<?php echo BASE_URL; ?>/actions/login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="email" name="email" placeholder="Seu e-mail" required>
                <input type="password" name="senha" placeholder="Sua senha" required>
                <button type="submit">Entrar</button>
            </form>
            <p style="margin-top: 15px;"><a href="esqueci_senha.php">Esqueci minha senha</a></p>
            <p class="form-switcher">Ainda não tem uma conta? <a id="show-cadastro">Cadastre-se</a></p>
        </div>

        <div class="form-box" id="cadastro-form-container" style="display: none;">
            <h2>Cadastro</h2>
            <form action="<?php echo BASE_URL; ?>/actions/processar_cad.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="nome" placeholder="Seu nome completo" required>
                <input type="email" name="email" placeholder="Crie seu e-mail" required>
                <input type="password" name="senha" placeholder="Crie sua senha (mín. 8 caracteres)" required minlength="8">
                <input type="password" name="confirmar_senha" placeholder="Confirme sua senha" required>
                <button type="submit">Cadastrar</button>
            </form>
            <p class="form-switcher">Já tem uma conta? <a id="show-login">Faça login</a></p>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginContainer = document.getElementById('login-form-container');
    const cadastroContainer = document.getElementById('cadastro-form-container');
    const showCadastroLink = document.getElementById('show-cadastro');
    const showLoginLink = document.getElementById('show-login');

    // Mostra o formulário de cadastro e esconde o de login
    showCadastroLink.addEventListener('click', function(e) {
        e.preventDefault();
        loginContainer.style.display = 'none';
        cadastroContainer.style.display = 'block';
    });

    // Mostra o formulário de login e esconde o de cadastro
    showLoginLink.addEventListener('click', function(e) {
        e.preventDefault();
        cadastroContainer.style.display = 'none';
        loginContainer.style.display = 'block';
    });

    // Lógica para mostrar o formulário correto em caso de erro na submissão
    <?php if (isset($_GET['erro']) && in_array($_GET['erro'], ['campos_vazios', 'senhas_nao_coincidem', 'senha_curta', 'email_existente'])): ?>
        loginContainer.style.display = 'none';
        cadastroContainer.style.display = 'block';
    <?php elseif (isset($_GET['erro']) && $_GET['erro'] == 'login_invalido'): ?>
        loginContainer.style.display = 'block';
        cadastroContainer.style.display = 'none';
    <?php endif; ?>
});
</script>

<?php 
require_once __DIR__ . '/../app/templates/footer.php';
?>