</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Saboroso Burger. Todos os direitos reservados.</p>
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