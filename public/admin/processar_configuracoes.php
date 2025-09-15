<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Acesso inválido.');
}

$horario_abertura = $_POST['horario_abertura'];
$horario_fechamento = $_POST['horario_fechamento'];

// Validação simples do formato de hora
if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $horario_abertura) || !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $horario_fechamento)) {
    die('Formato de hora inválido.');
}

$pdo->beginTransaction();
try {
    // Atualiza o horário de abertura
    $stmt_abertura = $pdo->prepare("UPDATE configuracoes SET valor = ? WHERE chave = 'HORARIO_ABERTURA'");
    $stmt_abertura->execute([$horario_abertura]);

    // Atualiza o horário de fechamento
    $stmt_fechamento = $pdo->prepare("UPDATE configuracoes SET valor = ? WHERE chave = 'HORARIO_FECHAMENTO'");
    $stmt_fechamento->execute([$horario_fechamento]);

    $pdo->commit();

    header('Location: ' . BASE_URL . '/admin/configuracoes.php?status=sucesso');
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao salvar configurações: " . $e->getMessage());
    die("Ocorreu um erro ao salvar as configurações. Por favor, tente novamente.");
}
?>