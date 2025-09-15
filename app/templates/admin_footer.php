</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- LÓGICA PARA O MENU DROPDOWN DO ADMIN ---
    const adminMenuButton = document.getElementById('admin-menu-button');
    const adminDropdown = document.getElementById('admin-dropdown');

    if (adminMenuButton && adminDropdown) {
        adminMenuButton.addEventListener('click', function(event) {
            event.stopPropagation();
            adminDropdown.classList.toggle('show');
            adminMenuButton.classList.toggle('active');
        });

        // Fecha o dropdown se o usuário clicar fora dele
        window.addEventListener('click', function(event) {
            if (!adminMenuButton.contains(event.target) && !adminDropdown.contains(event.target)) {
                if (adminDropdown.classList.contains('show')) {
                    adminDropdown.classList.remove('show');
                    adminMenuButton.classList.remove('active');
                }
            }
        });
    }
});
</script>

</body>
</html>