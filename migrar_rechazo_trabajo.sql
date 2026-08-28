USE impmartinez;

ALTER TABLE seguimiento_trabajos 
MODIFY COLUMN accion ENUM('recibido', 'inicio_reparacion', 'nota_tecnica', 'completado', 'pausado', 'rechazado') NOT NULL;
