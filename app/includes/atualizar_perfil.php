<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';

// Validações de segurança (código existente)
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação CSRF.');
}

$id_usuario = $_SESSION['usuario_id'];
$acao = $_POST['acao'] ?? '';

// --- LÓGICA PARA ATUALIZAR DADOS (NOME, EMAIL, TELEFONE, ENDEREÇO) ---
if ($acao === 'atualizar_dados') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    // NOVOS CAMPOS RECEBIDOS
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);

    if (empty($nome) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ' . BASE_URL . '/minha_conta.php?erro=dados_invalidos');
        exit();
    }

    try {
        // Verifica se o novo e-mail já pertence a outro usuário
        $stmt = $pdo->prepare("SELECT id FROM login WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id_usuario]);
        if ($stmt->fetch()) {
            header('Location: ' . BASE_URL . '/minha_conta.php?erro=email_existente');
            exit();
        }

        // ATUALIZA O COMANDO SQL PARA INCLUIR OS NOVOS CAMPOS
        $stmt = $pdo->prepare("UPDATE login SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $telefone, $endereco, $id_usuario]);

        // Atualiza o nome na sessão para refletir imediatamente no header
        $_SESSION['usuario_nome'] = $nome;

        header('Location: ' . BASE_URL . '/minha_conta.php?status=dados_ok');
        exit();

    } catch (PDOException $e) {
        die("Erro ao atualizar os dados: " . $e->getMessage());
    }
}

// --- LÓGICA PARA ATUALIZAR A SENHA (código existente sem alterações) ---
if ($acao === 'atualizar_senha') {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
        header('Location: ' . BASE_URL . '/minha_conta.php?erro=campos_vazios');
        exit();
    }
    if ($nova_senha !== $confirmar_senha) {
        header('Location: ' . BASE_URL . '/minha_conta.php?erro=senhas_nao_coincidem');
        exit();
    }
    if (strlen($nova_senha) < 8) {
        header('Location: ' . BASE_URL . '/minha_conta.php?erro=senha_curta');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT senha FROM login WHERE id = ?");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
            header('Location: ' . BASE_URL . '/minha_conta.php?erro=senha_atual_invalida');
            exit();
        }

        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE login SET senha = ? WHERE id = ?");
        $stmt->execute([$nova_senha_hash, $id_usuario]);

        header('Location: ' . BASE_URL . '/minha_conta.php?status=senha_ok');
        exit();

    } catch (PDOException $e) {
        die("Erro ao atualizar a senha.");
    }
}

// Se nenhuma ação válida for encontrada, redireciona
header('Location: ' . BASE_URL . '/minha_conta.php');
exit();
?>