<?php
class PedidoRepuesto extends Model {
    protected $table = 'pedidos_repuestos';
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca, r.categoria,
                u.nombre as solicitante_nombre, u.apellido_paterno as solicitante_ap,
                s.nombre as sucursal_nombre,
                t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_apellido,
                resp.nombre as respondido_nombre, resp.apellido_paterno as respondido_ap
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN usuarios u ON p.solicitado_por = u.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios t ON p.tecnico_id = t.id
                LEFT JOIN usuarios resp ON p.respondido_por = resp.id
                WHERE p.id = ?";
        return $this->fetchOne($sql, [$id]);
    }
    
    public function obtenerPorSucursal($sucursal_id) {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca,
                u.nombre as solicitante_nombre, u.apellido_paterno as solicitante_ap,
                s.nombre as sucursal_nombre,
                t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_apellido
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN usuarios u ON p.solicitado_por = u.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios t ON p.tecnico_id = t.id
                WHERE p.sucursal_id = ?
                ORDER BY p.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerTodos() {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca,
                u.nombre as solicitante_nombre, u.apellido_paterno as solicitante_ap,
                s.nombre as sucursal_nombre,
                t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_apellido
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN usuarios u ON p.solicitado_por = u.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios t ON p.tecnico_id = t.id
                ORDER BY p.fecha_solicitud DESC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerPorSolicitante($usuario_id) {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca,
                s.nombre as sucursal_nombre,
                t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_apellido,
                resp.nombre as respondido_nombre, resp.apellido_paterno as respondido_ap
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios t ON p.tecnico_id = t.id
                LEFT JOIN usuarios resp ON p.respondido_por = resp.id
                WHERE p.solicitado_por = ?
                ORDER BY p.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$usuario_id]);
    }
    
    public function obtenerPendientesAlmacen() {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca, r.categoria, r.stock, r.unidades_disponibles,
                u.nombre as solicitante_nombre, u.apellido_paterno as solicitante_ap,
                s.nombre as sucursal_nombre,
                t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_apellido
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN usuarios u ON p.solicitado_por = u.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios t ON p.tecnico_id = t.id
                WHERE p.estado = 'solicitado'
                ORDER BY p.fecha_solicitud ASC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerRespondidosPendientesConfirmacion() {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca,
                u.nombre as solicitante_nombre, u.apellido_paterno as solicitante_ap,
                s.nombre as sucursal_nombre,
                resp.nombre as respondido_nombre, resp.apellido_paterno as respondido_ap
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN usuarios u ON p.solicitado_por = u.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios resp ON p.respondido_por = resp.id
                WHERE p.estado IN ('enviando', 'no_existe', 'stock_agotado') AND p.confirmado = 0
                ORDER BY p.fecha_respuesta ASC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerRespondidosPorSolicitante($usuario_id) {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca,
                s.nombre as sucursal_nombre,
                resp.nombre as respondido_nombre, resp.apellido_paterno as respondido_ap
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios resp ON p.respondido_por = resp.id
                WHERE p.solicitado_por = ? AND p.estado IN ('enviando', 'no_existe', 'stock_agotado') AND p.confirmado = 0
                ORDER BY p.fecha_respuesta ASC";
        return $this->fetchAll($sql, [$usuario_id]);
    }
    
    public function contarPendientesAlmacen() {
        $sql = "SELECT COUNT(*) as total FROM pedidos_repuestos WHERE estado = 'solicitado'";
        $result = $this->fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    public function contarPendientesConfirmacion($usuario_id) {
        $sql = "SELECT COUNT(*) as total FROM pedidos_repuestos WHERE solicitado_por = ? AND estado IN ('enviando', 'no_existe', 'stock_agotado') AND confirmado = 0";
        $result = $this->fetchOne($sql, [$usuario_id]);
        return $result['total'] ?? 0;
    }
    
    public function responder($id, $tipo_respuesta, $respuesta_texto, $usuario_id) {
        $data = [
            'estado' => $tipo_respuesta,
            'tipo_respuesta' => $tipo_respuesta,
            'respuesta_texto' => $respuesta_texto,
            'respondido_por' => $usuario_id,
            'fecha_respuesta' => date('Y-m-d H:i:s')
        ];
        return $this->actualizar($id, $data);
    }
    
    public function confirmarRecibido($id, $usuario_id) {
        $data = [
            'estado' => 'confirmado',
            'confirmado' => 1,
            'fecha_confirmacion' => date('Y-m-d H:i:s')
        ];
        return $this->actualizar($id, $data);
    }
    
    public function confirmarLeido($id, $usuario_id) {
        $data = [
            'confirmado' => 1,
            'fecha_confirmacion' => date('Y-m-d H:i:s')
        ];
        return $this->actualizar($id, $data);
    }
    
    public function obtenerHistorialSolicitante($usuario_id, $filtros = []) {
        $sql = "SELECT p.*, 
                r.nombre as repuesto_nombre, r.codigo as repuesto_codigo, r.marca, r.categoria,
                s.nombre as sucursal_nombre,
                resp.nombre as respondido_nombre, resp.apellido_paterno as respondido_ap
                FROM pedidos_repuestos p
                JOIN repuestos r ON p.repuesto_id = r.id
                JOIN sucursales s ON p.sucursal_id = s.id
                LEFT JOIN usuarios resp ON p.respondido_por = resp.id
                WHERE p.solicitado_por = ?";
        
        $params = [$usuario_id];
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = ?";
            $params[] = $filtros['estado'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(p.fecha_solicitud) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(p.fecha_solicitud) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (r.nombre LIKE ? OR r.codigo LIKE ? OR r.marca LIKE ?)";
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        $sql .= " ORDER BY p.fecha_solicitud DESC";
        
        return $this->fetchAll($sql, $params);
    }
    
    public function contarPorEstadoSolicitante($usuario_id) {
        $sql = "SELECT estado, COUNT(*) as cantidad FROM pedidos_repuestos WHERE solicitado_por = ? GROUP BY estado";
        return $this->fetchAll($sql, [$usuario_id]);
    }
    
    public function obtenerMasPedidos($limite = 10) {
        $sql = "SELECT r.nombre, r.codigo, r.marca, SUM(p.cantidad) as total_pedidos 
                FROM pedidos_repuestos p 
                JOIN repuestos r ON p.repuesto_id = r.id 
                GROUP BY r.id, r.nombre, r.codigo, r.marca 
                ORDER BY total_pedidos DESC LIMIT ?";
        return $this->fetchAll($sql, [$limite]);
    }
}
