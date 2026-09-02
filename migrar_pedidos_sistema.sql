ALTER TABLE pedidos_repuestos 
ADD COLUMN tipo_respuesta ENUM('enviando','no_existe','stock_agotado') NULL AFTER estado,
ADD COLUMN respuesta_texto TEXT NULL AFTER tipo_respuesta,
ADD COLUMN respondido_por INT NULL AFTER respuesta_texto,
ADD COLUMN fecha_respuesta TIMESTAMP NULL AFTER respondido_por,
ADD COLUMN confirmado TINYINT(1) DEFAULT 0 AFTER fecha_respuesta,
ADD COLUMN fecha_confirmacion TIMESTAMP NULL AFTER confirmado,
MODIFY COLUMN estado ENUM('solicitado','enviando','no_existe','stock_agotado','enviado','confirmado') DEFAULT 'solicitado';

ALTER TABLE pedidos_repuestos ADD CONSTRAINT fk_pedidos_respondido FOREIGN KEY (respondido_por) REFERENCES usuarios(id);
