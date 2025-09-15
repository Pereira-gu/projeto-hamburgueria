<?php
// Inicia a sessão se ainda não houver uma
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e se é um administrador.
// Se não for, redireciona para a página inicial.
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // Inclui o config para ter acesso à BASE_URL
    require_once __DIR__ . '/../config/config.php';
    header("Location: " . BASE_URL . "/index.php");
    exit();
}
?>