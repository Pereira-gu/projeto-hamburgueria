</main>

<footer>
    <div class="container">
        <div class="footer-grid">

            <div class="footer-coluna">
                <h4>Sobre este Projeto</h4>
                <p>
                    O <strong>Saboroso Burger</strong> é um projeto de portfólio que demonstra um sistema de delivery full-stack, desde a vitrine de produtos para o cliente até o painel de gerenciamento para o administrador.
                </p>
            </div>

            <div class="footer-coluna">
                <h4>Tecnologias Utilizadas</h4>
                <ul>
                    <li><strong>Back-end:</strong> PHP</li>
                    <li><strong>Front-end:</strong> HTML, CSS, JavaScript (AJAX)</li>
                    <li><strong>Banco de Dados:</strong> MySQL</li>
                </ul>
            </div>

            <div class="footer-coluna">
                <h4>Desenvolvido por</h4>
                <p>Gustavo Pereira</p>
                <div class="footer-social">
                    <a href="https://github.com/Pereira-gu" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/gustavo-dos-santos-pereira-9b6471385/" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="https://www.instagram.com/_gu.pereira/?next=%2F" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/5511915072940" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="mailto:pereira.worker@gmail.com" title="E-mail"><i class="fas fa-envelope"></i></a>
                </div>
            </div>

        </div>
        <div class="footer-copyright">
            <p>Desenvolvido com ❤️ por Gustavo Pereira &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</footer>

<script>
    const AppConfig = {
        baseUrl: "<?php echo BASE_URL; ?>",
        csrfToken: "<?php echo $_SESSION['csrf_token'] ?? ''; ?>"
    };
</script>

<script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>

</body>

</html>