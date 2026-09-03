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
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca as repuesto_marca, r.unidades_disponibles,
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
    
    public function obtenerConFiltros($filtros = [], $limite = null) {
        $sql = "SELECT sc.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca as repuesto_marca, r.unidades_disponibles,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_ap
                FROM solicitudes_componentes sc
                JOIN equipos e ON sc.equipo_id = e.id
                JOIN repuestos r ON sc.repuesto_id = r.id
                JOIN clientes c ON e.cliente_id = c.id
                JOIN usuarios t ON sc.tecnico_id = t.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['dia'])) {
            $sql .= " AND DAY(sc.fecha_solicitud) = ?";
            $params[] = $filtros['dia'];
        }
        
        if (!empty($filtros['mes'])) {
            $sql .= " AND MONTH(sc.fecha_solicitud) = ?";
            $params[] = $filtros['mes'];
        }
        
        if (!empty($filtros['anio'])) {
            $sql .= " AND YEAR(sc.fecha_solicitud) = ?";
            $params[] = $filtros['anio'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND sc.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        $sql .= " ORDER BY sc.fecha_solicitud DESC";
        
        if ($limite) {
            $sql .= " LIMIT ?";
            $params[] = $limite;
        }
        
        return $this->fetchAll($sql, $params);
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
    
    public function contarPendientesTecnico($tecnico_id) {
        $sql = "SELECT COUNT(*) as total FROM solicitudes_componentes WHERE tecnico_id = ? AND estado IN ('solicitado', 'enviado')";
        $result = $this->fetchOne($sql, [$tecnico_id]);
        return $result['total'] ?? 0;
    }
    
    public function obtenerCostoTotalEquipo($equipo_id) {
        $sql = "SELECT COALESCE(SUM(total), 0) as total FROM solicitudes_componentes WHERE equipo_id = ? AND estado = 'recibido'";
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
    
    public function obtenerAgotadasTecnico($tecnico_id) {
        $sql = "SELECT sc.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       NULL as compra_externa_id, NULL as proveedor, NULL as ce_precio, NULL as ce_estado
                FROM solicitudes_componentes sc
                JOIN equipos e ON sc.equipo_id = e.id
                JOIN repuestos r ON sc.repuesto_id = r.id
                JOIN clientes c ON e.cliente_id = c.id
                WHERE sc.tecnico_id = ? AND sc.estado = 'agotado'
                ORDER BY sc.fecha_solicitud DESC";
        
        $columna_existe = $this->fetchOne("SELECT COUNT(*) as existe FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'solicitudes_componentes' AND column_name = 'compra_externa_id'");
        if (!empty($columna_existe['existe'])) {
            $tabla_existe = $this->fetchOne("SELECT COUNT(*) as existe FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'compras_externas'");
            if (!empty($tabla_existe['existe'])) {
                $sql = "SELECT sc.*, 
                               e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                               r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                               c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                               ce.id as compra_externa_id, ce.proveedor, ce.precio_unitario as ce_precio, ce.estado as ce_estado
                        FROM solicitudes_componentes sc
                        JOIN equipos e ON sc.equipo_id = e.id
                        JOIN repuestos r ON sc.repuesto_id = r.id
                        JOIN clientes c ON e.cliente_id = c.id
                        LEFT JOIN compras_externas ce ON sc.compra_externa_id = ce.id
                        WHERE sc.tecnico_id = ? AND sc.estado = 'agotado'
                        ORDER BY sc.fecha_solicitud DESC";
            }
        }
        
        return $this->fetchAll($sql, [$tecnico_id]);
    }
    
    public function actualizarCompraExternaId($solicitud_id, $compra_externa_id) {
        return $this->update(['compra_externa_id' => $compra_externa_id], "id = ?", [$solicitud_id]);
    }
    
    public function obtenerComprasExternas() {
        $sql = "SELECT ce.*, 
                       sc.equipo_id, sc.cantidad as solicitud_cantidad,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_ap
                FROM compras_externas ce
                JOIN solicitudes_componentes sc ON ce.solicitud_id = sc.id
                JOIN repuestos r ON ce.repuesto_id = r.id
                JOIN equipos e ON ce.equipo_id = e.id
                JOIN clientes c ON e.cliente_id = c.id
                JOIN usuarios t ON ce.tecnico_id = t.id
                ORDER BY ce.fecha_solicitud DESC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerCompraExternaPorId($id) {
        $sql = "SELECT ce.*, sc.equipo_id, r.nombre as repuesto_nombre
                FROM compras_externas ce
                JOIN solicitudes_componentes sc ON ce.solicitud_id = sc.id
                JOIN repuestos r ON ce.repuesto_id = r.id
                WHERE ce.id = ?";
        return $this->fetchOne($sql, [$id]);
    }
    
    public function crearCompraExterna($solicitud_id, $equipo_id, $repuesto_id, $tecnico_id, $cantidad, $precio_unitario, $proveedor) {
        $sql = "INSERT INTO compras_externas (solicitud_id, equipo_id, repuesto_id, tecnico_id, cantidad, precio_unitario, proveedor, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')";
        $this->query($sql, [
            $solicitud_id,
            $equipo_id,
            $repuesto_id,
            $tecnico_id,
            $cantidad,
            $precio_unitario,
            $proveedor
        ]);
        return $this->db->insert_id;
    }
    
    public function marcarCompraExternaRecibida($compra_id) {
        $sql = "UPDATE compras_externas SET estado = 'recibida', fecha_recibido = NOW() WHERE id = ?";
        $this->query($sql, [$compra_id]);
        return true;
    }
    
    public function actualizarEstadoSolicitud($id, $estado) {
        $sql = "UPDATE solicitudes_componentes SET estado = ? WHERE id = ?";
        $this->query($sql, [$estado, $id]);
        return true;
    }
}
