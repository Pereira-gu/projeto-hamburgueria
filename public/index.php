<?php 
require_once __DIR__ . '/../app/templates/header.php'; 
require_once __DIR__ . '/../app/includes/conexao.php'; // Adiciona a conexão para buscar categorias

try {
    $lista_categorias = $pdo->query("SELECT nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $lista_categorias = []; // Garante que a página não quebre se houver erro
}
?>

<div class="container page-content">
    <h1>O Nosso Menu</h1>

    <div class="filtros-container">
        <input type="text" id="busca" class="filtro-busca" placeholder="Procurar por nome...">
        <button class="filtro-btn ativo" data-categoria="todos">Todos</button>
        <?php foreach ($lista_categorias as $categoria): ?>
            <button class="filtro-btn" data-categoria="<?php echo htmlspecialchars($categoria['nome']); ?>">
                <?php echo htmlspecialchars($categoria['nome']); ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <?php 
    include 'menu.php'; 
    ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const campoBusca = document.getElementById('busca');
    const botoesFiltro = document.querySelectorAll('.filtro-btn');
    
    function filtrarItens() {
        const itensCardapio = document.querySelectorAll('.item');
        const termoBusca = campoBusca.value.toLowerCase();
        const categoriaAtiva = document.querySelector('.filtro-btn.ativo').dataset.categoria;

        itensCardapio.forEach(item => {
            const nomeItem = item.querySelector('h3').textContent.toLowerCase();
            const categoriaItem = item.dataset.categoria;

            const correspondeBusca = nomeItem.includes(termoBusca);
            const correspondeCategoria = (categoriaAtiva === 'todos' || categoriaItem === categoriaAtiva);

            if (correspondeBusca && correspondeCategoria) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    campoBusca.addEventListener('input', filtrarItens);

    botoesFiltro.forEach(botao => {
        botao.addEventListener('click', function() {
            botoesFiltro.forEach(btn => btn.classList.remove('ativo'));
            this.classList.add('ativo');
            filtrarItens();
        });
    });
});
</script>

<?php 
require_once __DIR__ . '/../app/templates/footer.php'; 
?>