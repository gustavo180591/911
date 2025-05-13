/**
 * Script para solucionar problemas que requieren presionar F5 para que las páginas funcionen
 * Este script detecta condiciones donde la página no se ha cargado correctamente
 * y fuerza una recarga cuando es necesario.
 */

(function() {
    // Función principal que se ejecuta cuando el DOM está listo
    function setupPageRefreshFix() {
        // Registrar cuándo se inicia la navegación
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('pageExitTime', Date.now());
        });

        // Comprobar si la página se cargó demasiado rápido (posible problema de caché)
        const pageExitTime = sessionStorage.getItem('pageExitTime');
        if (pageExitTime) {
            const loadTime = Date.now() - parseInt(pageExitTime);
            // Si la página cargó en menos de 100ms, podría ser una carga desde caché problemática
            if (loadTime < 100) {
                console.log('Carga sospechosamente rápida, verificando estado de la página...');
                verifyPageState();
            }
            sessionStorage.removeItem('pageExitTime');
        }

        // Verificar el estado de la página después de que se haya cargado completamente
        window.addEventListener('load', function() {
            setTimeout(verifyPageState, 500);
        });

        // Monitorear errores JavaScript que podrían indicar problemas de carga
        window.addEventListener('error', function(e) {
            console.error('Error detectado, podría requerir recarga:', e);
            // Incrementar contador de errores
            const errorCount = parseInt(sessionStorage.getItem('jsErrorCount') || '0') + 1;
            sessionStorage.setItem('jsErrorCount', errorCount);
            
            // Si hay múltiples errores, considerar recargar la página
            if (errorCount >= 3) {
                console.log('Múltiples errores detectados, recargando página...');
                sessionStorage.removeItem('jsErrorCount');
                reloadPage();
            }
        });

        // Agregar indicador a elementos clave que deberían inicializarse
        document.querySelectorAll('.should-initialize').forEach(function(el) {
            el.setAttribute('data-should-be-initialized', 'true');
        });
    }

    // Verificar si la página está en un estado correcto
    function verifyPageState() {
        // Verificar elementos que deberían haberse inicializado
        const uninitializedElements = document.querySelectorAll('[data-should-be-initialized="true"]:not(.initialized)');
        if (uninitializedElements.length > 0) {
            console.log('Elementos no inicializados detectados, recargando página...', uninitializedElements);
            reloadPage();
            return;
        }

        // Verificar si hay formularios o interacciones que no funcionan correctamente
        const brokenForms = document.querySelectorAll('form.needs-verification:not(.verified)');
        if (brokenForms.length > 0) {
            console.log('Formularios no verificados detectados, recargando página...', brokenForms);
            reloadPage();
            return;
        }

        // Verificar si hay contenido dinámico que debería haberse cargado
        const emptyContainers = document.querySelectorAll('.should-have-content:empty');
        if (emptyContainers.length > 0) {
            console.log('Contenedores vacíos detectados, recargando página...', emptyContainers);
            reloadPage();
            return;
        }

        console.log('Verificación de página completada, todo parece correcto');
    }

    // Recargar la página forzando bypass de caché
    function reloadPage() {
        console.log('Recargando página para corregir problemas...');
        // Establecer una bandera para evitar bucles de recarga
        if (sessionStorage.getItem('pageReloaded') === 'true') {
            console.warn('Ya se ha realizado una recarga, evitando bucle');
            return;
        }
        sessionStorage.setItem('pageReloaded', 'true');
        // Recargar sin caché
        window.location.reload(true);
    }

    // Limpiar la bandera de recarga cuando la página se ha cargado completamente
    window.addEventListener('load', function() {
        setTimeout(function() {
            sessionStorage.removeItem('pageReloaded');
        }, 1000);
    });

    // Iniciar el sistema cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupPageRefreshFix);
    } else {
        setupPageRefreshFix();
    }
})(); 