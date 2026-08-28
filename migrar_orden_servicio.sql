-- Migración para agregar campos de orden de servicio a la tabla equipos
USE impmartinez;

ALTER TABLE equipos 
ADD COLUMN costo_estimado DECIMAL(10,2) NULL AFTER equipo_reacondicionado,
ADD COLUMN observaciones TEXT NULL AFTER costo_estimado,
ADD COLUMN firma_digital LONGTEXT NULL AFTER observaciones,
ADD COLUMN fecha_conformidad TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER firma_digital;

-- Los campos agregados permiten:
-- costo_estimado: Precio estimado de la reparación
-- observaciones: Notas adicionales sobre el equipo o reparación
-- firma_digital: Firma del cliente en formato base64 (imagen PNG)
-- fecha_conformidad: Fecha y hora en que el cliente firmó la conformidad
