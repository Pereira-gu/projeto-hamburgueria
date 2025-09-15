<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

$nome_categoria = trim($_POST['nome']);

if (!empty($nome_categoria)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
        $stmt->execute([$nome_categoria]);
    } catch (PDOException $e) {
        // Ignora erro de chave duplicada, mas loga outros erros
        if($e->getCode() != 23000) {
            error_log("Erro ao criar categoria: " . $e->getMessage());
        }
    }
}

header('Location: ' . BASE_URL . '/admin/gerenciar_categorias.php');
exit();