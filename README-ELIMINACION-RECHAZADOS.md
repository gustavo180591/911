# Eliminación de la funcionalidad de Denuncias Rechazadas

Este documento describe los cambios realizados para eliminar la funcionalidad de denuncias rechazadas del sistema 911.

## Cambios realizados

1. Se eliminó el método `reject` en `DenunciaController.php` que permitía rechazar denuncias.
2. Se eliminó el botón y formulario de rechazo de denuncias en la vista de detalles (`templates/emergency/show.html.twig`).
3. Se creó un script SQL (`migrations/remove_rejected_state.sql`) para eliminar el estado "Rechazado" de la base de datos y reasignar las denuncias con ese estado a "Pendiente".
4. Se creó un comando de Symfony (`src/Command/RemoveRejectedStateCommand.php`) para ejecutar el script SQL de forma segura.

## Instrucciones para aplicar los cambios

### 1. Verificar los cambios en el código

Asegúrese de que los siguientes archivos han sido modificados correctamente:

- `src/Controller/DenunciaController.php` - Eliminación del método `reject`
- `templates/emergency/show.html.twig` - Eliminación del botón y formulario de rechazo

### 2. Ejecutar el comando de eliminación del estado Rechazado

Para eliminar el estado "Rechazado" de la base de datos y actualizar las denuncias existentes, ejecute el siguiente comando:

```bash
php bin/console app:remove-rejected-state
```

Este comando realizará las siguientes acciones:
- Asegurarse de que existe el estado "Pendiente"
- Actualizar todas las denuncias con estado "Rechazado" a "Pendiente"
- Eliminar el estado "Rechazado" de la base de datos
- Registrar la migración en una tabla de registro

### 3. Verificar los cambios

Después de ejecutar el comando, es recomendable verificar:

1. Que no exista el estado "Rechazado" en la tabla `estado_denuncia`
2. Que todas las denuncias anteriormente en estado "Rechazado" ahora estén en estado "Pendiente"
3. Que la interfaz de usuario ya no muestre la opción de rechazar denuncias

## Contacto para soporte

Si encuentra algún problema al aplicar estos cambios, por favor contacte al equipo de desarrollo. 