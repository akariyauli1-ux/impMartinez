-- Tabla de auditoría de almacén
CREATE TABLE IF NOT EXISTS almacen_auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion ENUM('crear', 'editar', 'eliminar', 'enviar_pedido') NOT NULL,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_id INT,
    descripcion TEXT,
    datos_antiguos JSON,
    datos_nuevos JSON,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
