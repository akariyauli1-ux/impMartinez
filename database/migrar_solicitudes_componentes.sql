-- Tabla para solicitudes de componentes por parte de técnicos
CREATE TABLE IF NOT EXISTS solicitudes_componentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    motivo TEXT,
    estado ENUM('solicitado', 'aprobado', 'rechazado', 'entregado') DEFAULT 'solicitado',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id) ON DELETE CASCADE
);

-- Agregar campo para costo total de reparación en equipos
ALTER TABLE equipos ADD COLUMN IF NOT EXISTS costo_reparacion DECIMAL(10,2) DEFAULT 0.00;
