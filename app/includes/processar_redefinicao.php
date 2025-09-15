<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Acesso inválido.');
}

$token = $_POST['token'];
$nova_senha = $_POST['nova_senha'];
$confirmar_senha = $_POST['confirmar_senha'];

if ($nova_senha !== $confirmar_senha || strlen($nova_senha) < 8) {
    header('Location: ' . BASE_URL . '/redefinir_senha.php?token=' . urlencode($token) . '&erro=validacao');
    exit();
}

$pdo->beginTransaction();
try {
    // Valida o token novamente
    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires >= ?");
    $stmt->execute([$token, time()]);
    $reset_request = $stmt->fetch();

    if (!$reset_request) {
        throw new Exception("Token inválido ou expirado.");
    }

    $email = $reset_request['email'];
    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Atualiza a senha do usuário na tabela 'login'
    $stmt_update = $pdo->prepare("UPDATE login SET senha = ? WHERE email = ?");
    $stmt_update->execute([$nova_senha_hash, $email]);

    // Deleta o token para que não possa ser usado novamente
    $stmt_delete = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt_delete->execute([$email]);

    $pdo->commit();

    // Redireciona para a página de login com mensagem de sucesso
    header('Location: ' . BASE_URL . '/cadastro.php?status=senha_redefinida');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Erro ao redefinir senha: " . $e->getMessage());
    header('Location: ' . BASE_URL . '/redefinir_senha.php?token=' . urlencode($token) . '&erro=db');
    exit();
}