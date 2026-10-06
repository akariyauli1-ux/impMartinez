USE laboratorioia;

CREATE TABLE IF NOT EXISTS calificaciones_tecnicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tecnico_id INT NOT NULL,
    jefe_tecnico_id INT NOT NULL,
    mes INT NOT NULL,
    anio INT NOT NULL,
    puntuacion_trabajo DECIMAL(3,2) NOT NULL,
    puntuacion_asistencia DECIMAL(3,2) NOT NULL DEFAULT 0,
    puntuacion_total DECIMAL(3,2) NOT NULL,
    observaciones TEXT,
    fecha_calificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (jefe_tecnico_id) REFERENCES usuarios(id),
    UNIQUE KEY unique_tecnico_mes (tecnico_id, mes, anio)
);
