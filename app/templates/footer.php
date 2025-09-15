</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Saboroso Burger. Todos os direitos reservados.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $_SESSION['csrf_token'] ?? ''; ?>';

    // --- LÓGICA DROPDOWN DE USUÁRIO ---
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');
    if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', function(event) {
            event.stopPropagation();
            userDropdown.classList.toggle('show');
            userMenuButton.classList.toggle('active');
        });
    }

    // --- LÓGICA PARA O MENU DROPDOWN DO ADMIN ---
    const adminMenuButton = document.getElementById('admin-menu-button');
    const adminDropdown = document.getElementById('admin-dropdown');
    if (adminMenuButton && adminDropdown) {
        adminMenuButton.addEventListener('click', function(event) {
            event.stopPropagation();
            adminDropdown.classList.toggle('show');
            adminMenuButton.classList.toggle('active');
        });
    }

    // --- LÓGICA GLOBAL PARA FECHAR DROPDOWNS AO CLICAR FORA ---
    window.addEventListener('click', function(event) {
        if (userDropdown && userDropdown.classList.contains('show') && !userMenuButton.contains(event.target)) {
            userDropdown.classList.remove('show');
            userMenuButton.classList.remove('active');
        }
        if (adminDropdown && adminDropdown.classList.contains('show') && !adminMenuButton.contains(event.target)) {
            adminDropdown.classList.remove('show');
            adminMenuButton.classList.remove('active');
        }
    });

    // --- LÓGICA DE NOTIFICAÇÃO "TOAST" ---
    const toastContainer = document.getElementById('toast-container');
    function showToast(mensagem, isError = false) {
        if (!toastContainer) return;
        const toast = document.createElement('div');
        toast.className = 'toast' + (isError ? ' error' : '');
        toast.textContent = mensagem;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3000);
    }

    // --- LÓGICA PARA ADICIONAR AO CARRINHO (AJAX) ---
    document.body.addEventListener('click', function(e) {
        // Alteração aqui: procuramos pela nova classe 'js-add-to-cart'
        if (e.target && e.target.classList.contains('js-add-to-cart')) {
            e.preventDefault();
            const form = e.target.closest('form');
            if (!form) return;

            const button = e.target;
            const originalText = button.textContent;
            button.textContent = '...';
            button.disabled = true;

            const formData = new FormData(form);
            
            fetch(form.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    showToast('Adicionado ao carrinho!');
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.total_carrinho;
                    }
                } else {
                    showToast(data.mensagem || 'Ocorreu um erro.', true);
                    if (data.redirecionar) {
                        window.location.href = data.redirecionar;
                    }
                }
            })
            .catch(error => {
                console.error('Erro no fetch:', error);
                showToast('Erro de conexão.', true);
            })
            .finally(() => {
                button.textContent = originalText;
                button.disabled = false;
            });
        }
    });

    // --- LÓGICA PARA ATUALIZAR QUANTIDADE NO CARRINHO ---
    const paginaCarrinho = document.getElementById('pagina-carrinho');
    if (paginaCarrinho) {
        paginaCarrinho.addEventListener('click', function(e) {
            const target = e.target;
            const isQuantityBtn = target.classList.contains('quantity-btn');
            const isRemoveLink = target.classList.contains('action-delete-item');

            if (!isQuantityBtn && !isRemoveLink) return;
            e.preventDefault();

            const carrinhoId = target.dataset.carrinhoId;
            const tableRow = target.closest('tr');
            const quantitySpan = tableRow.querySelector('.quantity-value');
            let quantidadeAtual = parseInt(quantitySpan.textContent);
            let novaQuantidade;

            if (isRemoveLink) {
                novaQuantidade = 0;
            } else {
                novaQuantidade = (target.dataset.action === 'increase') ? quantidadeAtual + 1 : quantidadeAtual - 1;
            }

            if (novaQuantidade < 0) return;

            atualizarQuantidade(carrinhoId, novaQuantidade, tableRow);
        });
    }

    function atualizarQuantidade(carrinhoId, quantidade, tableRow) {
        const formData = new FormData();
        formData.append('carrinho_id', carrinhoId);
        formData.append('quantidade', quantidade);
        formData.append('csrf_token', csrfToken);

        fetch('<?php echo BASE_URL; ?>/actions/atualizar_carrinho.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                const cartCount = document.getElementById('cart-count');
                const valorTotalCarrinho = document.getElementById('valor-total-carrinho');
                
                cartCount.textContent = data.total_itens_carrinho;

                if (data.item_removido) {
                    tableRow.remove();
                } else {
                    tableRow.querySelector('.quantity-value').textContent = quantidade;
                    tableRow.querySelector('.subtotal-valor').textContent = data.novo_subtotal_formatado;
                }
                
                if (data.carrinho_vazio) {
                    document.getElementById('conteudo-carrinho').innerHTML = `
                        <div class="carrinho-vazio">
                            <h2>Seu carrinho está vazio.</h2>
                            <a href="<?php echo BASE_URL; ?>/index.php" class="btn-login" style="margin-top: 20px;">Ver cardápio</a>
                        </div>`;
                } else if (valorTotalCarrinho) {
                     valorTotalCarrinho.textContent = data.novo_total_formatado;
                }
            } else {
                alert(data.mensagem || 'Ocorreu um erro ao atualizar o carrinho.');
            }
        })
        .catch(error => {
            console.error('Erro no fetch:', error);
            alert('Erro de conexão ao tentar atualizar o carrinho.');
        });
    }
});
</script>

</body>
</html>