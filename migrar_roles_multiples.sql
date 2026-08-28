-- Migración para sistema de roles múltiples
USE impmartinez;

-- Crear tabla de roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar roles existentes
INSERT INTO roles (nombre, descripcion) VALUES
('recepcionista', 'Encargado de recepción de equipos'),
('tecnico', 'Técnico de reparación'),
('admin_sucursal', 'Administrador de sucursal'),
('jefe_tecnico', 'Jefe de técnicos'),
('almacenista', 'Encargado de almacén'),
('gerente', 'Gerente general'),
('rrhh', 'Recursos Humanos')
ON DUPLICATE KEY UPDATE nombre = nombre;

-- Crear tabla intermedia usuario_roles
CREATE TABLE IF NOT EXISTS usuario_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_rol (usuario_id, rol_id)
);

-- Migrar datos existentes de la columna rol a la tabla usuario_roles
INSERT INTO usuario_roles (usuario_id, rol_id)
SELECT u.id, r.id 
FROM usuarios u
JOIN roles r ON r.nombre = u.rol
ON DUPLICATE KEY UPDATE usuario_id = usuario_id;

-- Crear vista para obtener usuarios con sus roles
CREATE OR REPLACE VIEW v_usuarios_roles AS
SELECT 
    u.id,
    u.nombre,
    u.apellido_paterno,
    u.apellido_materno,
    u.carnet,
    u.email,
    u.telefono,
    u.sucursal_id,
    u.activo,
    u.fecha_creacion,
    s.nombre as sucursal_nombre,
    GROUP_CONCAT(r.nombre ORDER BY r.nombre SEPARATOR ',') as roles,
    GROUP_CONCAT(r.id ORDER BY r.id SEPARATOR ',') as roles_ids,
    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as nombre_completo
FROM usuarios u
LEFT JOIN sucursales s ON u.sucursal_id = s.id
LEFT JOIN usuario_roles ur ON u.id = ur.usuario_id
LEFT JOIN roles r ON ur.rol_id = r.id AND r.activo = 1
GROUP BY u.id, u.nombre, u.apellido_paterno, u.apellido_materno, u.carnet, u.email, u.telefono, u.sucursal_id, u.activo, u.fecha_creacion, s.nombre;
