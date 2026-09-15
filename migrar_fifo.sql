USE laboratorioia;

CREATE TABLE lotes_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL,
    cantidad_restante INT NOT NULL,
    precio_compra DECIMAL(10,2) NOT NULL,
    fecha_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    almacenista_id INT NOT NULL,
    motivo TEXT,
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id) ON DELETE CASCADE,
    FOREIGN KEY (almacenista_id) REFERENCES usuarios(id)
);

ALTER TABLE repuestos ADD COLUMN precio_promedio DECIMAL(10,2) DEFAULT 0 AFTER precio_unitario;
