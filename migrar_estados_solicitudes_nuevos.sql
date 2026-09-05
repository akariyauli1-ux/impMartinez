-- Agregar estados 'enviado' y 'recibido' al ENUM de estado
ALTER TABLE solicitudes_repuestos_nuevos MODIFY estado ENUM('pendiente', 'creado', 'comprado_externo', 'enviado', 'recibido', 'cancelado') DEFAULT 'pendiente';
