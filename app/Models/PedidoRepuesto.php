<?php
class PedidoRepuesto extends Model {
    protected $table = 'pedidos_repuestos';
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function obtenerPorSucursal($sucursal_id) {
        $sql = "SELECT p.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca, u.nombre as solicitante_nombre, u.apellido_paterno, s.nombre as sucursal_nombre, t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_apellido FROM pedidos_repuestos p JOIN repuestos r ON p.repuesto_id = r.id JOIN usuarios u ON p.solicitado_por = u.id JOIN sucursales s ON p.sucursal_id = s.id LEFT JOIN usuarios t ON p.tecnico_id = t.id WHERE p.sucursal_id = ? ORDER BY p.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerTodos() {
        $sql = "SELECT p.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca, u.nombre as solicitante_nombre, u.apellido_paterno, s.nombre as sucursal_nombre, t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_apellido FROM pedidos_repuestos p JOIN repuestos r ON p.repuesto_id = r.id JOIN usuarios u ON p.solicitado_por = u.id JOIN sucursales s ON p.sucursal_id = s.id LEFT JOIN usuarios t ON p.tecnico_id = t.id ORDER BY p.fecha_solicitud DESC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerMasPedidos($limite = 10) {
        $sql = "SELECT r.nombre, r.codigo, r.marca, SUM(p.cantidad) as total_pedidos FROM pedidos_repuestos p JOIN repuestos r ON p.repuesto_id = r.id GROUP BY r.id, r.nombre, r.codigo, r.marca ORDER BY total_pedidos DESC LIMIT ?";
        return $this->fetchAll($sql, [$limite]);
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
}
