<?php
class Repuesto extends Model {
    protected $table = 'repuestos';
    
    public function obtenerPorSucursal($sucursal_id) {
        $sql = "SELECT * FROM repuestos WHERE sucursal_id = ? ORDER BY categoria, nombre";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerPorId($id) {
        return $this->fetchOne("SELECT * FROM repuestos WHERE id = ?", [$id]);
    }
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
    
    public function obtenerStockBajo($sucursal_id) {
        $sql = "SELECT COUNT(*) as total FROM repuestos WHERE sucursal_id = ? AND stock <= stock_minimo";
        $result = $this->fetchOne($sql, [$sucursal_id]);
        return $result['total'] ?? 0;
    }
}
