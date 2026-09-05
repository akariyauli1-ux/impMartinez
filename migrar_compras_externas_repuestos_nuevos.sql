-- Migracion para soportar compras externas de repuestos nuevos
ALTER TABLE compras_externas MODIFY repuesto_id INT NULL;
ALTER TABLE compras_externas ADD COLUMN solicitud_repuesto_nuevo_id INT NULL AFTER solicitud_id;
ALTER TABLE compras_externas ADD COLUMN nombre_repuesto VARCHAR(255) NULL AFTER repuesto_id;
ALTER TABLE solicitudes_repuestos_nuevos ADD COLUMN compra_externa_id INT NULL AFTER repuesto_id;
