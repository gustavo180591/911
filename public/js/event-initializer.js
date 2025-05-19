/**
 * Script para garantizar la inicialización correcta de eventos en todas las páginas
 * Resuelve problemas donde los manejadores de eventos no se registran correctamente
 */

(function() {
    // Objeto para rastrear qué inicializadores ya se han ejecutado
    const EventInitializer = {
        initializedModules: {},
        
        // Registrar un inicializador para un módulo específico
        register: function(moduleName, initFunction) {
            if (typeof initFunction !== 'function') {
                console.error(`Inicializador para ${moduleName} debe ser una función`);
                return;
            }
            
            // Ejecutar inmediatamente si el DOM ya está listo
            if (document.readyState !== 'loading') {
                this._runInitializer(moduleName, initFunction);
            } else {
                // Programar para ejecutarse cuando el DOM esté listo
                document.addEventListener('DOMContentLoaded', () => {
                    this._runInitializer(moduleName, initFunction);
                });
            }
        },
        
        // Forzar la reinicialización de un módulo específico
        reinitialize: function(moduleName) {
            const initFn = this.initializedModules[moduleName];
            if (initFn) {
                console.log(`Reinicializando módulo: ${moduleName}`);
                initFn();
                return true;
            }
            return false;
        },
        
        // Reinicializar todos los módulos registrados
        reinitializeAll: function() {
            console.log('Reinicializando todos los módulos');
            for (const moduleName in this.initializedModules) {
                this.reinitialize(moduleName);
            }
        },
        
        // Ejecutar un inicializador y registrarlo
        _runInitializer: function(moduleName, initFunction) {
            try {
                initFunction();
                this.initializedModules[moduleName] = initFunction;
                console.log(`Módulo ${moduleName} inicializado correctamente`);
            } catch (error) {
                console.error(`Error al inicializar módulo ${moduleName}:`, error);
            }
        }
    };

    // Exponer el inicializador globalmente
    window.EventInitializer = EventInitializer;

    // Detectar problemas de navegación y reinicializar si es necesario
    window.addEventListener('load', function() {
        const pageLoadTimestamp = Date.now();
        
        // Verificar si hay elementos que necesitan inicialización pero no la han recibido
        setTimeout(function() {
            const uninitializedElements = document.querySelectorAll('[data-module]:not(.initialized)');
            if (uninitializedElements.length > 0) {
                console.warn('Se detectaron elementos sin inicializar:', uninitializedElements);
                
                // Intentar reinicializar sus módulos correspondientes
                uninitializedElements.forEach(el => {
                    const moduleName = el.getAttribute('data-module');
                    if (moduleName && EventInitializer.initializedModules[moduleName]) {
                        EventInitializer.reinitialize(moduleName);
                    }
                });
            }
        }, 500);
    });
})(); 