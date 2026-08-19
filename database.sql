-- Base de datos impMartinez
CREATE DATABASE IF NOT EXISTS impmartinez CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE impmartinez;

-- Tabla de sucursales
CREATE TABLE sucursales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de usuarios (todos los roles)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NOT NULL,
    carnet VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    rol ENUM('recepcionista', 'tecnico', 'admin_sucursal', 'jefe_tecnico', 'almacenista', 'gerente', 'rrhh') NOT NULL,
    sucursal_id INT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL
);

-- Tabla de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50),
    dni VARCHAR(20),
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    direccion VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de equipos
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    tipo_equipo ENUM('celular', 'laptop', 'pc', 'tv', 'radio', 'otro') NOT NULL,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    numero_serie VARCHAR(100),
    accesorios TEXT,
    descripcion_falla TEXT,
    fotos JSON,
    estado ENUM('registrado', 'pendiente_asignacion', 'asignado_sucursal', 'en_reparacion', 'completado', 'entregado') DEFAULT 'registrado',
    recepcionista_id INT,
    sucursal_origen_id INT,
    sucursal_actual_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (recepcionista_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (sucursal_origen_id) REFERENCES sucursales(id) ON DELETE SET NULL,
    FOREIGN KEY (sucursal_actual_id) REFERENCES sucursales(id) ON DELETE SET NULL
);

-- Tabla de asignaciones de equipos a sucursales
CREATE TABLE asignaciones_sucursal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    sucursal_origen_id INT NOT NULL,
    sucursal_destino_id INT NOT NULL,
    admin_origen_id INT NOT NULL,
    motivo TEXT,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (sucursal_origen_id) REFERENCES sucursales(id),
    FOREIGN KEY (sucursal_destino_id) REFERENCES sucursales(id),
    FOREIGN KEY (admin_origen_id) REFERENCES usuarios(id)
);

-- Tabla de asignaciones de trabajos a técnicos
CREATE TABLE asignaciones_tecnico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    jefe_tecnico_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id),
    FOREIGN KEY (jefe_tecnico_id) REFERENCES usuarios(id)
);

-- Tabla de seguimiento de trabajos
CREATE TABLE seguimiento_trabajos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    accion ENUM('recibido', 'inicio_reparacion', 'nota_tecnica', 'completado', 'pausado') NOT NULL,
    descripcion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id)
);

-- Tabla de inventario de repuestos
CREATE TABLE repuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50),
    stock INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    precio_unitario DECIMAL(10,2),
    sucursal_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id) ON DELETE SET NULL
);

-- Tabla de movimientos de inventario
CREATE TABLE movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repuesto_id INT NOT NULL,
    tipo ENUM('entrada', 'salida') NOT NULL,
    cantidad INT NOT NULL,
    motivo TEXT,
    almacenista_id INT NOT NULL,
    equipo_id INT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id) ON DELETE CASCADE,
    FOREIGN KEY (almacenista_id) REFERENCES usuarios(id),
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE SET NULL
);

-- Tabla de asistencia del personal
CREATE TABLE asistencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_entrada TIME,
    hora_salida TIME,
    estado ENUM('presente', 'tardanza', 'ausente', 'permiso') DEFAULT 'presente',
    observaciones TEXT,
    registrado_por INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id),
    UNIQUE KEY unique_fecha_usuario (fecha, usuario_id)
);

-- Tabla de inspecciones de limpieza y uniforme
CREATE TABLE inspecciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    limpieza ENUM('aprobado', 'observado', 'rechazado') DEFAULT 'aprobado',
    uniforme ENUM('completo', 'incompleto', 'observado') DEFAULT 'completo',
    observaciones TEXT,
    registrado_por INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

-- Tabla de pedidos de repuestos
CREATE TABLE pedidos_repuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sucursal_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL,
    estado ENUM('solicitado', 'aprobado', 'enviado', 'recibido') DEFAULT 'solicitado',
    solicitado_por INT NOT NULL,
    aprobado_por INT,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_aprobacion TIMESTAMP NULL,
    FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id),
    FOREIGN KEY (solicitado_por) REFERENCES usuarios(id),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id)
);

-- Insertar sucursales de ejemplo
INSERT INTO sucursales (nombre, direccion, telefono) VALUES
('Sucursal Centro', 'Av. Principal 123', '555-0101'),
('Sucursal Norte', 'Calle Norte 456', '555-0102'),
('Sucursal Sur', 'Av. Sur 789', '555-0103');

-- Insertar usuario gerente (apellido: Admin, carnet: 0001)
INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, carnet, email, rol, sucursal_id) VALUES
('Gerente', 'Admin', 'Sistema', '0001', 'gerente@impmartinez.com', 'gerente', 1);

-- Insertar usuario RRHH (apellido: Admin, carnet: 0002)
INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, carnet, email, rol, sucursal_id) VALUES
('RRHH', 'Admin', 'Sistema', '0002', 'rrhh@impmartinez.com', 'rrhh', 1);

-- Insertar usuario de ejemplo para cada sucursal (admin)
INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, carnet, email, rol, sucursal_id) VALUES
('Admin', 'Sucursal1', 'Demo', '1001', 'admin1@impmartinez.com', 'admin_sucursal', 1),
('Admin', 'Sucursal2', 'Demo', '1002', 'admin2@impmartinez.com', 'admin_sucursal', 2),
('Admin', 'Sucursal3', 'Demo', '1003', 'admin3@impmartinez.com', 'admin_sucursal', 3);
