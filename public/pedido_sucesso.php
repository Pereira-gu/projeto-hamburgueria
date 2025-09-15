<?php
require_once __DIR__ . '/../app/templates/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}
?>
<main>
    <div class="container page-content" style="max-width: 600px; text-align: center;">
        <div class="sucesso-box" style="background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <h1 style="color: #28a745; font-size: 2.5rem;">Obrigado!</h1>
            <p style="font-size: 1.2rem; margin: 20px 0;">Seu pedido foi recebido com sucesso!</p>
            <p>Já estamos preparando tudo com muito carinho. Entraremos em contato para confirmar a entrega e o pagamento.</p>
            <br>
            <a href="<?php echo BASE_URL; ?>/index.php" class="btn-login" style="display: inline-block; margin-top: 20px;">Voltar ao Cardápio</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../app/templates/footer.php'; ?>