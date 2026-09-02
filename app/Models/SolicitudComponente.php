<?php
class SolicitudComponente extends Model {
    protected $table = 'solicitudes_componentes';
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function obtenerPorEquipo($equipo_id) {
        $sql = "SELECT sc.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca 
                FROM solicitudes_componentes sc
                JOIN repuestos r ON sc.repuesto_id = r.id
                WHERE sc.equipo_id = ?
                ORDER BY sc.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$equipo_id]);
    }
    
    public function obtenerPorTecnico($tecnico_id) {
        $sql = "SELECT sc.*, e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap
                FROM solicitudes_componentes sc
                JOIN equipos e ON sc.equipo_id = e.id
                JOIN repuestos r ON sc.repuesto_id = r.id
                JOIN clientes c ON e.cliente_id = c.id
                WHERE sc.tecnico_id = ?
                ORDER BY sc.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$tecnico_id]);
    }
    
    public function obtenerTodas() {
        $sql = "SELECT sc.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca as repuesto_marca,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_ap
                FROM solicitudes_componentes sc
                JOIN equipos e ON sc.equipo_id = e.id
                JOIN repuestos r ON sc.repuesto_id = r.id
                JOIN clientes c ON e.cliente_id = c.id
                JOIN usuarios t ON sc.tecnico_id = t.id
                ORDER BY sc.fecha_solicitud DESC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerPendientes() {
        $sql = "SELECT sc.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca as repuesto_marca,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_ap
                FROM solicitudes_componentes sc
                JOIN equipos e ON sc.equipo_id = e.id
                JOIN repuestos r ON sc.repuesto_id = r.id
                JOIN clientes c ON e.cliente_id = c.id
                JOIN usuarios t ON sc.tecnico_id = t.id
                WHERE sc.estado = 'solicitado'
                ORDER BY sc.fecha_solicitud ASC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerCostoTotalEquipo($equipo_id) {
        $sql = "SELECT COALESCE(SUM(total), 0) as total FROM solicitudes_componentes WHERE equipo_id = ?";
        $result = $this->fetchOne($sql, [$equipo_id]);
        return $result['total'] ?? 0;
    }
    
    public function actualizarCostoEquipo($equipo_id) {
        $costo = $this->obtenerCostoTotalEquipo($equipo_id);
        $sql = "UPDATE equipos SET costo_reparacion = ? WHERE id = ?";
        return $this->query($sql, [$costo, $equipo_id]);
    }
    
    public function actualizarEstado($id, $estado) {
        return $this->update(['estado' => $estado], "id = ?", [$id]);
    }
    
    public function obtenerPorIdConDetalles($id) {
        $sql = "SELECT sc.*, r.nombre as repuesto_nombre, r.id as repuesto_id
                FROM solicitudes_componentes sc
                JOIN repuestos r ON sc.repuesto_id = r.id
                WHERE sc.id = ?";
        return $this->fetchOne($sql, [$id]);
    }
    
    public function contarPendientesAlmacen() {
        $sql = "SELECT COUNT(*) as total FROM solicitudes_componentes WHERE estado = 'solicitado'";
        $result = $this->fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    public function contarEnviadasTecnico($tecnico_id) {
        $sql = "SELECT COUNT(*) as total FROM solicitudes_componentes WHERE tecnico_id = ? AND estado = 'enviado' AND notificacion_leida = 0";
        $result = $this->fetchOne($sql, [$tecnico_id]);
        return $result['total'] ?? 0;
    }
    
    public function marcarNotificacionesLeidas($tecnico_id) {
        $sql = "UPDATE solicitudes_componentes SET notificacion_leida = 1 WHERE tecnico_id = ? AND estado = 'enviado' AND notificacion_leida = 0";
        return $this->query($sql, [$tecnico_id]);
    }
    
    public function confirmarRecibido($id, $tecnico_id) {
        $sql = "UPDATE solicitudes_componentes SET estado = 'recibido', fecha_recibido = NOW() WHERE id = ? AND tecnico_id = ? AND estado = 'enviado'";
        return $this->query($sql, [$id, $tecnico_id]);
    }
    
    public function obtenerEnviadasTecnico($tecnico_id) {
        $sql = "SELECT sc.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap
                FROM solicitudes_componentes sc
                JOIN equipos e ON sc.equipo_id = e.id
                JOIN repuestos r ON sc.repuesto_id = r.id
                JOIN clientes c ON e.cliente_id = c.id
                WHERE sc.tecnico_id = ? AND sc.estado = 'enviado'
                ORDER BY sc.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$tecnico_id]);
    }
}
