<?php
class MovimientoInventario extends Model {
    protected $table = 'movimientos_inventario';
    
    public function registrar($data) {
        return $this->insert($data);
    }
    
    public function obtenerPorSucursal($sucursal_id, $limite = 50) {
        $sql = "SELECT m.*, r.nombre as repuesto_nombre, u.nombre as almacenista_nombre FROM movimientos_inventario m JOIN repuestos r ON m.repuesto_id = r.id JOIN usuarios u ON m.almacenista_id = u.id WHERE r.sucursal_id = ? ORDER BY m.fecha_movimiento DESC LIMIT ?";
        return $this->fetchAll($sql, [$sucursal_id, $limite]);
    }
}
