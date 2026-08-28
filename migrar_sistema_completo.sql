-- Migración completa del sistema de gestión de reparaciones
-- Fecha: 2026-03-24

USE impmartinez;

-- ============================================
-- CAMPOS ADICIONALES EN TABLA EQUIPOS
-- ============================================

-- Estados de componentes
ALTER TABLE equipos ADD COLUMN estado_pantalla VARCHAR(20) NULL AFTER estado;
ALTER TABLE equipos ADD COLUMN estado_carga VARCHAR(20) NULL AFTER estado_pantalla;
ALTER TABLE equipos ADD COLUMN estado_puertos VARCHAR(20) NULL AFTER estado_carga;
ALTER TABLE equipos ADD COLUMN estado_case VARCHAR(20) NULL AFTER estado_puertos;
ALTER TABLE equipos ADD COLUMN estado_touch VARCHAR(20) NULL AFTER estado_case;
ALTER TABLE equipos ADD COLUMN estado_camara VARCHAR(20) NULL AFTER estado_touch;
ALTER TABLE equipos ADD COLUMN estado_encendido VARCHAR(20) NULL AFTER estado_camara;
ALTER TABLE equipos ADD COLUMN marco_doblado VARCHAR(20) NULL AFTER estado_encendido;
ALTER TABLE equipos ADD COLUMN estado_parlantes VARCHAR(20) NULL AFTER marco_doblado;
ALTER TABLE equipos ADD COLUMN estado_imagenes VARCHAR(20) NULL AFTER estado_parlantes;

-- Estado físico
ALTER TABLE equipos ADD COLUMN previamente_abierto VARCHAR(10) NULL AFTER estado_imagenes;
ALTER TABLE equipos ADD COLUMN contacto_liquidos VARCHAR(10) NULL AFTER previamente_abierto;
ALTER TABLE equipos ADD COLUMN equipo_reacondicionado VARCHAR(10) NULL AFTER contacto_liquidos;

-- Orden de servicio
ALTER TABLE equipos ADD COLUMN costo_estimado DECIMAL(10,2) NULL AFTER equipo_reacondicionado;
ALTER TABLE equipos ADD COLUMN observaciones TEXT NULL AFTER costo_estimado;
ALTER TABLE equipos ADD COLUMN firma_digital LONGTEXT NULL AFTER observaciones;
ALTER TABLE equipos ADD COLUMN fecha_conformidad TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER firma_digital;

-- ============================================
-- TABLA EQUIPOS_FOTOS
-- ============================================

CREATE TABLE IF NOT EXISTS equipos_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    foto_data LONGBLOB NOT NULL,
    foto_tipo VARCHAR(50) NOT NULL,
    orden INT DEFAULT 0,
    tipo VARCHAR(20) DEFAULT 'general',
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE
);

-- ============================================
-- ÍNDICES PARA MEJORAR RENDIMIENTO
-- ============================================

CREATE INDEX idx_equipos_cliente ON equipos(cliente_id);
CREATE INDEX idx_equipos_estado ON equipos(estado);
CREATE INDEX idx_equipos_fecha ON equipos(fecha_registro);
CREATE INDEX idx_equipos_recepcionista ON equipos(recepcionista_id);

-- ============================================
-- VALORES POSIBLES PARA LOS CAMPOS
-- ============================================

-- Estados de componentes (estado_pantalla, estado_carga, etc.):
-- 'buen_estado', 'mal_estado', 'no_aplica'

-- Estado físico (previamente_abierto, contacto_liquidos, equipo_reacondicionado):
-- 'si', 'no', 'no_sabe'

-- Tipo de foto (equipos_fotos.tipo):
-- 'anverso', 'reverso', 'general'
