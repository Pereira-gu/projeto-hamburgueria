<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Calcula o total de itens no carrinho para exibir no contador
$total_itens_carrinho = 0;
if (isset($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/../includes/conexao.php';
    $stmt_count = $pdo->prepare("SELECT SUM(quantidade) as total FROM carrinho WHERE login_id = ?");
    $stmt_count->execute([$_SESSION['usuario_id']]);
    $resultado = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $total_itens_carrinho = $resultado['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saboroso Burger</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/login.css">
</head>

<body>

    <div class="portfolio-banner">
        <div class="container">
            <p><strong>Atenção:</strong> Este é um projeto de portfólio para fins de demonstração. Nenhum pedido será processado.</p>
        </div>
    </div>

    <div id="toast-container"></div>

    <header>
        <div class="container">
            <a href="<?php echo BASE_URL; ?>/index.php" class="logo">Saboroso Burger</a>

            <nav class="main-nav-links">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Cardápio</a></li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/carrinho.php" style="display: flex; align-items: center; gap: 8px;">
                            Carrinho
                            <span id="cart-count" class="cart-badge"><?php echo $total_itens_carrinho; ?></span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="user-area">
                <?php if (isset($_SESSION['usuario_id'])):
                    $nomeCompleto = $_SESSION['usuario_nome'];
                    $partesNome = explode(' ', $nomeCompleto);
                    $primeiroNome = $partesNome[0];
                ?>
                    <div class="user-menu-container">
                        <button id="user-menu-button" class="user-menu-button">
                            Olá, <?php echo htmlspecialchars($primeiroNome); ?>
                            <span class="arrow-down">▼</span>
                        </button>
                        <div id="user-dropdown" class="user-menu-dropdown">
                            <a href="<?php echo BASE_URL; ?>/meus_pedidos.php">Meus Pedidos</a>
                            <a href="<?php echo BASE_URL; ?>/minha_conta.php">Minha Conta</a>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <a href="<?php echo BASE_URL; ?>/admin/index.php">Painel Admin</a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>/actions/logout.php" class="logout-link">Sair</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/cadastro.php" class="btn-login">Login / Cadastrar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>