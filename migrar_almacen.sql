-- Migración para el módulo de almacén mejorado
USE impmartinez;

-- Agregar nuevos campos a la tabla repuestos
ALTER TABLE repuestos 
ADD COLUMN codigo VARCHAR(50) UNIQUE AFTER id,
ADD COLUMN marca VARCHAR(100) AFTER nombre,
ADD COLUMN clave_producto VARCHAR(100) AFTER marca,
ADD COLUMN movimiento_salida INT DEFAULT 0 AFTER precio_unitario,
ADD COLUMN solicitudes INT DEFAULT 0 AFTER movimiento_salida,
ADD COLUMN ventas INT DEFAULT 0 AFTER solicitudes,
ADD COLUMN inversion DECIMAL(10,2) DEFAULT 0 AFTER ventas,
ADD COLUMN unidades_disponibles INT DEFAULT 0 AFTER inversion,
ADD COLUMN descontinuado TINYINT(1) DEFAULT 0 AFTER stock_minimo;

-- Agregar nuevos campos a la tabla pedidos_repuestos
ALTER TABLE pedidos_repuestos
ADD COLUMN tecnico_id INT AFTER solicitado_por,
ADD COLUMN precio_unitario DECIMAL(10,2) AFTER cantidad,
ADD COLUMN total DECIMAL(10,2) AFTER precio_unitario,
ADD FOREIGN KEY (tecnico_id) REFERENCES usuarios(id);

-- Crear vista para estadísticas de pedidos por sucursal
CREATE OR REPLACE VIEW v_pedidos_sucursal AS
SELECT 
    s.id,
    s.nombre,
    COUNT(p.id) as total_pedidos,
    SUM(p.cantidad) as total_unidades,
    SUM(p.total) as total_monto
FROM sucursales s
LEFT JOIN pedidos_repuestos p ON s.id = p.sucursal_id
GROUP BY s.id, s.nombre;

-- Crear vista para repuestos más solicitados
CREATE OR REPLACE VIEW v_repuestos_mas_solicitados AS
SELECT 
    r.id,
    r.nombre,
    r.codigo,
    r.marca,
    r.categoria,
    SUM(p.cantidad) as total_solicitado,
    COUNT(p.id) as numero_pedidos
FROM repuestos r
LEFT JOIN pedidos_repuestos p ON r.id = p.repuesto_id
GROUP BY r.id, r.nombre, r.codigo, r.marca, r.categoria
ORDER BY total_solicitado DESC;
