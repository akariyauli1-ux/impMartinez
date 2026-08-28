USE impmartinez;

ALTER TABLE equipos 
ADD COLUMN entregado_por INT NULL,
ADD COLUMN fecha_entrega DATETIME NULL,
ADD FOREIGN KEY (entregado_por) REFERENCES usuarios(id) ON DELETE SET NULL;
