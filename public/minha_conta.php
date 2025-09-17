<?php
require_once __DIR__ . '/../app/templates/header.php';
require_once __DIR__ . '/../app/includes/conexao.php';

// 1. Garante que apenas usuários logados acessem a página
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/cadastro.php?status=login_necessario');
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// 2. Busca os dados atuais do usuário, incluindo os novos campos
try {
    $stmt = $pdo->prepare("SELECT nome, email, endereco, telefone FROM login WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar suas informações.");
}

// 3. Exibe mensagens de sucesso ou erro (código existente)
$mensagem_sucesso = '';
$mensagem_erro = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'dados_ok') $mensagem_sucesso = 'Seus dados foram atualizados com sucesso!';
    if ($_GET['status'] == 'senha_ok') $mensagem_sucesso = 'Sua senha foi alterada com sucesso!';
}
if (isset($_GET['erro'])) {
    if ($_GET['erro'] == 'senha_atual_invalida') $mensagem_erro = 'A senha atual informada está incorreta.';
    if ($_GET['erro'] == 'senhas_nao_coincidem') $mensagem_erro = 'As novas senhas não coincidem.';
    if ($_GET['erro'] == 'email_existente') $mensagem_erro = 'O novo e-mail informado já está em uso por outra conta.';
}
?>

<div class="container page-content">
    <h1>Minha Conta</h1>

    <?php if ($mensagem_sucesso): ?><div class="mensagem-feedback mensagem-sucesso"><?php echo $mensagem_sucesso; ?></div><?php endif; ?>
    <?php if ($mensagem_erro): ?><div class="mensagem-feedback mensagem-erro"><?php echo $mensagem_erro; ?></div><?php endif; ?>

    <div class="conta-container">
        <div class="conta-nav">
            <button class="conta-nav-btn active" data-target="dados-form-container">Meus Dados</button>
            <button class="conta-nav-btn" data-target="senha-form-container">Alterar Senha</button>
        </div>

        <div id="dados-form-container" class="form-box conta-form active">
            <form action="<?php echo BASE_URL; ?>/actions/atualizar_perfil.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="acao" value="atualizar_dados">

                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>

                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>" placeholder="(11) 99999-8888">

                <label for="endereco">Endereço Principal</label>
                <textarea id="endereco" name="endereco" placeholder="Rua das Flores, 123, Bairro, Cidade - SP, CEP 12345-678"><?php echo htmlspecialchars($usuario['endereco'] ?? ''); ?></textarea>
                <button type="submit" style="margin-top: 20px;">Salvar Alterações</button>
            </form>
        </div>

        <div id="senha-form-container" class="form-box conta-form">
            <form action="<?php echo BASE_URL; ?>/actions/atualizar_perfil.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="acao" value="atualizar_senha">

                <label for="senha_atual">Senha Atual</label>
                <input type="password" id="senha_atual" name="senha_atual" required>

                <label for="nova_senha">Nova Senha (mín. 8 caracteres)</label>
                <input type="password" id="nova_senha" name="nova_senha" required minlength="8">

                <label for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>

                <button type="submit">Alterar Senha</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navButtons = document.querySelectorAll('.conta-nav-btn');
        const formContainers = document.querySelectorAll('.conta-form');

        navButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove a classe 'active' de todos os botões e formulários
                navButtons.forEach(btn => btn.classList.remove('active'));
                formContainers.forEach(form => form.classList.remove('active'));

                // Adiciona a classe 'active' ao botão clicado e ao formulário correspondente
                this.classList.add('active');
                const targetId = this.dataset.target;
                document.getElementById(targetId).classList.add('active');
            });
        });

        // Verifica se há um erro relacionado à senha para já mostrar a aba correta
        <?php if (isset($_GET['erro']) && in_array($_GET['erro'], ['senha_atual_invalida', 'senhas_nao_coincidem', 'senha_curta', 'campos_vazios'])): ?>
            // Simula um clique no botão de alterar senha para abrir a aba correta
            document.querySelector('.conta-nav-btn[data-target="senha-form-container"]').click();
        <?php endif; ?>
        // --- LÓGICA DA MÁSCARA DE TELEFONE ---
        const telefoneContaEl = document.getElementById('telefone');
        if (telefoneContaEl) {
            IMask(telefoneContaEl, {
                mask: [{
                        mask: '(00) 0000-0000'
                    },
                    {
                        mask: '(00) 00000-0000'
                    }
                ]
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../app/templates/footer.php'; ?>