// Usar el sistema de inicialización de eventos para garantizar que el menú de perfil funcione correctamente
// sin necesidad de presionar F5

// Detectar si tenemos disponible el inicializador de eventos
if (window.EventInitializer) {
    // Registrar el inicializador del menú de perfil
    window.EventInitializer.register('profileMenu', initializeProfileMenu);
} else {
    // Fallback: inicializar directamente si el sistema de eventos no está disponible
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProfileMenu);
    } else {
        initializeProfileMenu();
    }
}

// Función principal de inicialización del menú de perfil
function initializeProfileMenu() {
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileButton && profileDropdown) {
        // Marcar el elemento como necesitado de inicialización para el sistema de auto-recarga
        profileButton.classList.add('should-initialize');
        
        // Remover listeners existentes para evitar duplicación
        const newProfileButton = profileButton.cloneNode(true);
        profileButton.parentNode.replaceChild(newProfileButton, profileButton);
        
        // Añadir listener al nuevo botón
        newProfileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
            
            // Marcar como inicializado para el sistema de auto-recarga
            newProfileButton.classList.add('initialized');
        });
        
        // Cerrar el dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !newProfileButton.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
        
        console.log('Menú de perfil inicializado correctamente');
    } else {
        console.warn('No se encontraron elementos del menú de perfil');
    }
} 