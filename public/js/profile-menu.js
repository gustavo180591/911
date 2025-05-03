document.addEventListener('DOMContentLoaded', function() {
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileButton && profileDropdown) {
        // Función para mostrar el menú
        function showDropdown() {
            profileDropdown.classList.add('show');
        }

        // Función para ocultar el menú
        function hideDropdown() {
            profileDropdown.classList.remove('show');
        }

        // Mostrar el menú al hacer clic en el botón
        profileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            showDropdown();
        });
        
        // Ocultar el menú al hacer clic fuera de él
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileButton.contains(e.target)) {
                hideDropdown();
            }
        });

        // Ocultar el menú al hacer clic en una opción
        profileDropdown.addEventListener('click', function(e) {
            if (e.target.classList.contains('dropdown-item')) {
                hideDropdown();
            }
        });

        // Ocultar el menú al presionar la tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDropdown();
            }
        });
    }
}); 