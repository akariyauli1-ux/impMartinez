<?php
class PedidoRepuesto extends Model {
    protected $table = 'pedidos_repuestos';
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function obtenerPorSucursal($sucursal_id) {
        $sql = "SELECT p.*, r.nombre as repuesto_nombre, u.nombre as solicitante FROM pedidos_repuestos p JOIN repuestos r ON p.repuesto_id = r.id JOIN usuarios u ON p.solicitado_por = u.id WHERE p.sucursal_id = ? ORDER BY p.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerMasPedidos($limite = 10) {
        $sql = "SELECT r.nombre, SUM(p.cantidad) as total_pedidos FROM pedidos_repuestos p JOIN repuestos r ON p.repuesto_id = r.id GROUP BY r.id ORDER BY total_pedidos DESC LIMIT ?";
        return $this->fetchAll($sql, [$limite]);
    }
}
