USE impmartinez;

ALTER TABLE equipos 
MODIFY COLUMN estado ENUM('registrado', 'pendiente_asignacion', 'asignado_sucursal', 'recibido', 'en_reparacion', 'completado', 'entregado') DEFAULT 'registrado';

ALTER TABLE seguimiento_trabajos 
MODIFY COLUMN accion ENUM('recibido', 'inicio_reparacion', 'nota_tecnica', 'completado', 'pausado', 'rechazado') NOT NULL;
