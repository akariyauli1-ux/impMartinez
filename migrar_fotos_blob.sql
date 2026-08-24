-- Agregar columnas para almacenar fotos como BLOB
ALTER TABLE usuarios 
ADD COLUMN foto_data LONGBLOB NULL AFTER foto,
ADD COLUMN foto_tipo VARCHAR(50) NULL AFTER foto_data;

-- Agregar columnas para logo de sucursal
ALTER TABLE sucursales 
ADD COLUMN logo_empresa_data LONGBLOB NULL AFTER logo_empresa,
ADD COLUMN logo_empresa_tipo VARCHAR(50) NULL AFTER logo_empresa_data;

-- Crear tabla para fotos de equipos
CREATE TABLE IF NOT EXISTS equipos_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    foto_data LONGBLOB NOT NULL,
    foto_tipo VARCHAR(50) NOT NULL,
    orden INT DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE
);
