-- Migración para tabla de solicitudes de repuestos que no existen en almacén
CREATE TABLE IF NOT EXISTS solicitudes_repuestos_nuevos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    nombre_repuesto VARCHAR(255) NOT NULL,
    marca VARCHAR(100) DEFAULT NULL,
    descripcion TEXT DEFAULT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    motivo TEXT DEFAULT NULL,
    estado ENUM('pendiente', 'creado', 'comprado_externo', 'cancelado') DEFAULT 'pendiente',
    precio_unitario DECIMAL(10,2) DEFAULT 0.00,
    repuesto_id INT DEFAULT NULL,
    proveedor VARCHAR(255) DEFAULT NULL,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_procesado DATETIME DEFAULT NULL,
    procesado_por INT DEFAULT NULL,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id) ON DELETE SET NULL,
    FOREIGN KEY (procesado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);
