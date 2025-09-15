<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

if (!isset($_GET['token']) || !isset($_SESSION['csrf_token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    die('Erro de validação de segurança.');
}

$id_produto = $_GET['id'] ?? null;

if (!$id_produto) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt_select = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmt_select->execute([$id_produto]);
    $produto = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $nome_imagem = $produto['imagem'];
        $caminho_imagem = __DIR__ . '/../assets/images/' . $nome_imagem;

        if (file_exists($caminho_imagem)) {
            unlink($caminho_imagem);
        }
    }

    $sql = "DELETE FROM produtos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_produto]);
    
    $pdo->commit();

    header("Location: " . BASE_URL . "/admin/index.php?status=excluido");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erro ao excluir produto: " . $e->getMessage());
    header("Location: " . BASE_URL . "/admin/index.php?status=erro_excluir");
    exit();
}
?>