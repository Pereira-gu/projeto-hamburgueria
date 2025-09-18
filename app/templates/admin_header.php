<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <div class="container">
            <a href="<?php echo BASE_URL; ?>/admin/index.php" class="logo">Admin Saboroso</a>

            <nav class="admin-nav">
                <a href="<?php echo BASE_URL; ?>/admin/index.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/gerenciar_pedidos.php">Pedidos</a>
                <a href="<?php echo BASE_URL; ?>/admin/gerenciar_produtos.php">Produtos</a>

                <div class="admin-menu-container">
                    <button id="admin-menu-button" class="admin-menu-button">
                        Gerenciar
                        <span class="arrow-down">▼</span>
                    </button>
                    <div id="admin-dropdown" class="admin-menu-dropdown">
                        <a href="<?php echo BASE_URL; ?>/admin/gerenciar_opcionais.php">Opcionais</a>
                        <a href="<?php echo BASE_URL; ?>/admin/gerenciar_categorias.php">Categorias</a>
                        <a href="<?php echo BASE_URL; ?>/admin/gerenciar_usuarios.php">Usuários</a>
                        <a href="<?php echo BASE_URL; ?>/admin/configuracoes.php">Configurações</a>
                    </div>
                </div>
            </nav>

            <div class="user-area">
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn-login">Ver Site</a>
                <a href="<?php echo BASE_URL; ?>/actions/logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
    </header>
    <main>