CREATE TABLE IF NOT EXISTS compras_externas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NOT NULL,
    equipo_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    proveedor VARCHAR(200) DEFAULT '',
    estado ENUM('pendiente', 'recibida', 'cancelada') DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_recibido DATETIME NULL,
    INDEX idx_solicitud (solicitud_id),
    INDEX idx_equipo (equipo_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE solicitudes_componentes ADD COLUMN IF NOT EXISTS compra_externa_id INT DEFAULT NULL AFTER estado;

ALTER TABLE solicitudes_componentes MODIFY COLUMN estado ENUM('solicitado','enviado','recibido','agotado','entregado') DEFAULT 'solicitado';
