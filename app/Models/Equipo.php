<?php
class Equipo extends Model {
    protected $table = 'equipos';
    
    public function obtenerPorId($id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel FROM equipos e JOIN clientes c ON e.cliente_id = c.id WHERE e.id = ?";
        return $this->fetchOne($sql, [$id]);
    }
    
    public function obtenerPendientesPorSucursal($sucursal_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap FROM equipos e JOIN clientes c ON e.cliente_id = c.id WHERE e.sucursal_actual_id = ? AND e.estado = 'pendiente_asignacion' ORDER BY e.fecha_registro ASC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerAsignadosSinTecnico($sucursal_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap FROM equipos e JOIN clientes c ON e.cliente_id = c.id WHERE e.sucursal_actual_id = ? AND e.estado = 'asignado_sucursal' AND e.id NOT IN (SELECT equipo_id FROM asignaciones_tecnico) ORDER BY e.fecha_registro ASC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerTrabajosTecnico($tecnico_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, at.fecha_asignacion FROM equipos e JOIN clientes c ON e.cliente_id = c.id JOIN asignaciones_tecnico at ON e.id = at.equipo_id WHERE at.tecnico_id = ? AND e.estado NOT IN ('entregado') ORDER BY e.fecha_registro DESC";
        return $this->fetchAll($sql, [$tecnico_id]);
    }
    
    public function obtenerHistorial($equipo_id, $tecnico_id = null) {
        if ($tecnico_id) {
            $sql = "SELECT * FROM seguimiento_trabajos WHERE equipo_id = ? AND tecnico_id = ? ORDER BY fecha_registro ASC";
            return $this->fetchAll($sql, [$equipo_id, $tecnico_id]);
        }
        $sql = "SELECT * FROM seguimiento_trabajos WHERE equipo_id = ? ORDER BY fecha_registro ASC";
        return $this->fetchAll($sql, [$equipo_id]);
    }
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
    
    public function obtenerEstadosPorSucursal($sucursal_id) {
        $sql = "SELECT estado, COUNT(*) as cantidad FROM equipos WHERE sucursal_actual_id = ? GROUP BY estado";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerRegistradosPor($recepcionista_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, s.nombre as sucursal_nombre FROM equipos e JOIN clientes c ON e.cliente_id = c.id LEFT JOIN sucursales s ON e.sucursal_actual_id = s.id WHERE e.recepcionista_id = ? ORDER BY e.fecha_registro DESC";
        return $this->fetchAll($sql, [$recepcionista_id]);
    }
    
    public function obtenerCountPorEstado($estado, $sucursal_id = null) {
        if ($sucursal_id) {
            $sql = "SELECT COUNT(*) as total FROM equipos WHERE estado = ? AND sucursal_actual_id = ?";
            $result = $this->fetchOne($sql, [$estado, $sucursal_id]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM equipos WHERE estado = ?";
            $result = $this->fetchOne($sql, [$estado]);
        }
        return $result['total'] ?? 0;
    }
}
