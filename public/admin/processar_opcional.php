<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexao.php';
require_once __DIR__ . '/../../app/includes/auth_admin.php';

$grupo = trim($_POST['grupo']);
$nome = trim($_POST['nome']);
$preco = $_POST['preco'] ?? 0.00;

if (!empty($grupo) && !empty($nome)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO opcionais (grupo, nome, preco) VALUES (?, ?, ?)");
        $stmt->execute([$grupo, $nome, $preco]);
    } catch (PDOException $e) {
        error_log("Erro ao criar opcional: " . $e->getMessage());
    }
}

header('Location: ' . BASE_URL . '/admin/gerenciar_opcionais.php');
exit();