-- Primero, obtener el ID del estado "Pendiente"
SET @pendiente_id = (SELECT id FROM estado_denuncia WHERE nombre = 'Pendiente' LIMIT 1);

-- Si no existe el estado "Pendiente", crearlo
INSERT INTO estado_denuncia (nombre, descripcion)
SELECT 'Pendiente', 'Denuncia pendiente de revisión'
WHERE NOT EXISTS (SELECT 1 FROM estado_denuncia WHERE nombre = 'Pendiente');

-- Actualizar nuevamente para asegurar que tenemos el ID
SET @pendiente_id = (SELECT id FROM estado_denuncia WHERE nombre = 'Pendiente' LIMIT 1);

-- Actualizar todas las denuncias con estado "Rechazado" a "Pendiente"
UPDATE denuncia d
JOIN estado_denuncia e ON d.estado_id = e.id
SET d.estado_id = @pendiente_id
WHERE e.nombre = 'Rechazado';

-- Eliminar el estado "Rechazado"
DELETE FROM estado_denuncia WHERE nombre = 'Rechazado';

-- Log de la migración
INSERT INTO migration_log (description, executed_at) VALUES ('Eliminación del estado Rechazado', NOW()); 