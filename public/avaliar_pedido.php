<?php
require_once __DIR__ . '/../app/templates/header.php';
require_once __DIR__ . '/../app/includes/conexao.php';

// Validações
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/cadastro.php');
    exit();
}
$id_usuario = $_SESSION['usuario_id'];
$id_pedido = $_GET['pedido_id'] ?? null;
if (!$id_pedido) {
    header('Location: ' . BASE_URL . '/meus_pedidos.php');
    exit();
}

try {
    // Busca os itens do pedido para serem avaliados
    $sql = "SELECT pi.produto_id, p.nome_produto, p.imagem
            FROM pedido_itens pi
            JOIN produtos p ON pi.produto_id = p.id
            JOIN pedidos ped ON pi.pedido_id = ped.id
            WHERE pi.pedido_id = ? AND ped.login_id = ? AND ped.status = 'Concluído'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pedido, $id_usuario]);
    $itens_para_avaliar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($itens_para_avaliar)) {
        // Se o pedido não existe, não pertence ao usuário ou não está concluído, redireciona.
        header('Location: ' . BASE_URL . '/meus_pedidos.php?erro=avaliacao_invalida');
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao carregar o pedido para avaliação.");
}
?>
<style>
.avaliacao-form .item-para-avaliar { display: flex; align-items: flex-start; gap: 20px; padding: 20px; margin-bottom: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.avaliacao-form img { border-radius: 5px; }
.avaliacao-form .item-info h3 { margin: 0 0 15px; }
.star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
.star-rating input[type="radio"] { display: none; }
.star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
.star-rating input[type="radio"]:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #ffc107; }
.avaliacao-form textarea { width: 100%; min-height: 80px; margin-top: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
</style>

<div class="container page-content">
    <h1>Avaliar Pedido #<?php echo htmlspecialchars($id_pedido); ?></h1>
    <p>Sua opinião é muito importante para nós!</p>

    <form class="avaliacao-form" action="<?php echo BASE_URL; ?>/actions/processar_avaliacao.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($id_pedido); ?>">

        <?php foreach($itens_para_avaliar as $item): ?>
            <div class="item-para-avaliar">
                <img src="<?php echo BASE_URL . '/assets/images/' . htmlspecialchars($item['imagem']); ?>" width="100" alt="<?php echo htmlspecialchars($item['nome_produto']); ?>">
                <div class="item-info" style="width: 100%;">
                    <h3><?php echo htmlspecialchars($item['nome_produto']); ?></h3>
                    
                    <label>Sua nota:</label>
                    <div class="star-rating">
                        <input type="radio" id="star5-<?php echo $item['produto_id']; ?>" name="notas[<?php echo $item['produto_id']; ?>]" value="5" required><label for="star5-<?php echo $item['produto_id']; ?>">★</label>
                        <input type="radio" id="star4-<?php echo $item['produto_id']; ?>" name="notas[<?php echo $item['produto_id']; ?>]" value="4"><label for="star4-<?php echo $item['produto_id']; ?>">★</label>
                        <input type="radio" id="star3-<?php echo $item['produto_id']; ?>" name="notas[<?php echo $item['produto_id']; ?>]" value="3"><label for="star3-<?php echo $item['produto_id']; ?>">★</label>
                        <input type="radio" id="star2-<?php echo $item['produto_id']; ?>" name="notas[<?php echo $item['produto_id']; ?>]" value="2"><label for="star2-<?php echo $item['produto_id']; ?>">★</label>
                        <input type="radio" id="star1-<?php echo $item['produto_id']; ?>" name="notas[<?php echo $item['produto_id']; ?>]" value="1"><label for="star1-<?php echo $item['produto_id']; ?>">★</label>
                    </div>

                    <label for="comentario-<?php echo $item['produto_id']; ?>" style="margin-top: 15px; display: block;">Seu comentário (opcional):</label>
                    <textarea name="comentarios[<?php echo $item['produto_id']; ?>]" id="comentario-<?php echo $item['produto_id']; ?>" placeholder="Conte o que você achou deste produto..."></textarea>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn-login" style="padding: 15px 30px; font-size: 1.1rem;">Enviar Avaliação</button>
    </form>
</div>

<?php
require_once __DIR__ . '/../app/templates/footer.php';
?>