/**
 * Script para garantizar la inicialización correcta de eventos en todas las páginas
 * Resuelve problemas donde los manejadores de eventos no se registran correctamente,
 * lo que requiere presionar F5 para que funcionen.
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
        
        // Ejecutar una función de inicialización y guardar referencia
        _runInitializer: function(moduleName, initFunction) {
            console.log(`Inicializando módulo: ${moduleName}`);
            try {
                initFunction();
                // Guardar referencia para posible reinicialización
                this.initializedModules[moduleName] = initFunction;
                
                // Marcar el elemento relacionado como inicializado
                document.querySelectorAll(`[data-module="${moduleName}"]`).forEach(el => {
                    el.classList.add('initialized');
                });
            } catch (e) {
                console.error(`Error al inicializar ${moduleName}:`, e);
            }
        }
    };
    
    // Exponer al ámbito global
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
                
                // Si aún hay elementos sin inicializar después de un segundo intento, sugerir recarga
                setTimeout(function() {
                    const stillUninitializedElements = document.querySelectorAll('[data-module]:not(.initialized)');
                    if (stillUninitializedElements.length > 0) {
                        console.error('Elementos que siguen sin inicializar después de reintentos:', stillUninitializedElements);
                        
                        // Verificar si ya hemos recargado recientemente para evitar bucle
                        const lastReload = sessionStorage.getItem('lastForceReload');
                        const currentTime = Date.now();
                        
                        if (!lastReload || (currentTime - parseInt(lastReload)) > 10000) {
                            console.log('Forzando recarga para resolver problemas de inicialización');
                            sessionStorage.setItem('lastForceReload', currentTime.toString());
                            window.location.reload(true);
                        }
                    }
                }, 1000);
            }
        }, 500);
    });
})(); 