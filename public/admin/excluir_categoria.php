<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if (!isset($_GET['token']) || !isset($_SESSION['csrf_token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação de segurança.');
}

$id_categoria = $_GET['id'] ?? null;
if (!$id_categoria) {
    header("Location: " . BASE_URL . "/admin/gerenciar_categorias.php");
    exit();
}

try {
    // 1. Pega o nome da categoria que será excluída
    $stmt_nome = $pdo->prepare("SELECT nome FROM categorias WHERE id = ?");
    $stmt_nome->execute([$id_categoria]);
    $categoria = $stmt_nome->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {
        header("Location: " . BASE_URL . "/admin/gerenciar_categorias.php");
        exit();
    }
    $nome_categoria = $categoria['nome'];

    // 2. VERIFICAÇÃO DE SEGURANÇA: Conta quantos produtos usam esta categoria
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria = ?");
    $stmt_check->execute([$nome_categoria]);
    $total_produtos = $stmt_check->fetchColumn();

    if ($total_produtos > 0) {
        // Se houver produtos, redireciona com uma mensagem de erro
        header("Location: " . BASE_URL . "/admin/gerenciar_categorias.php?status=erro_produtos_vinculados");
        exit();
    }

    // 3. Se não houver produtos, exclui a categoria
    $stmt_delete = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt_delete->execute([$id_categoria]);

    header("Location: " . BASE_URL . "/admin/gerenciar_categorias.php?status=excluido");
    exit();

} catch (PDOException $e) {
    error_log("Erro ao excluir categoria: " . $e->getMessage());
    header("Location: " . BASE_URL . "/admin/gerenciar_categorias.php?status=erro");
    exit();
}
?>