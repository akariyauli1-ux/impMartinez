USE impmartinez;

CREATE TABLE IF NOT EXISTS limpieza_local (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    areas_limpiadas TEXT NOT NULL,
    productos_utilizados TEXT,
    observaciones TEXT,
    registrado_por INT NOT NULL,
    sucursal_id INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id),
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);
