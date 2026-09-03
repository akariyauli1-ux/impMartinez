<?php
class Equipo extends Model {
    protected $table = 'equipos';
    
    public function obtenerPorId($id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel FROM equipos e JOIN clientes c ON e.cliente_id = c.id WHERE e.id = ?";
        $equipo = $this->fetchOne($sql, [$id]);
        
        if ($equipo && !empty($equipo['firma_entrega'])) {
            $equipo['firma_entrega'] = 'data:image/png;base64,' . base64_encode($equipo['firma_entrega']);
        }
        
        return $equipo;
    }
    
    public function obtenerPendientesPorSucursal($sucursal_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap FROM equipos e JOIN clientes c ON e.cliente_id = c.id WHERE e.sucursal_actual_id = ? AND e.estado IN ('pendiente_asignacion', 'recibido') ORDER BY e.fecha_registro ASC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerAsignadosSinTecnico($sucursal_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel 
                FROM equipos e 
                JOIN clientes c ON e.cliente_id = c.id 
                WHERE e.sucursal_actual_id = ? 
                AND e.estado IN ('pendiente_asignacion', 'asignado_sucursal') 
                AND e.id NOT IN (SELECT equipo_id FROM asignaciones_tecnico) 
                ORDER BY e.fecha_registro ASC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerTrabajosTecnico($tecnico_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, at.fecha_asignacion FROM equipos e JOIN clientes c ON e.cliente_id = c.id JOIN asignaciones_tecnico at ON e.id = at.equipo_id WHERE at.tecnico_id = ? AND e.estado NOT IN ('entregado') ORDER BY e.fecha_registro DESC";
        return $this->fetchAll($sql, [$tecnico_id]);
    }
    
    public function obtenerTrabajosTecnicoConFiltros($tecnico_id, $filtros = []) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, at.fecha_asignacion 
                FROM equipos e 
                JOIN clientes c ON e.cliente_id = c.id 
                JOIN asignaciones_tecnico at ON e.id = at.equipo_id 
                WHERE at.tecnico_id = ?";
        
        $params = [$tecnico_id];
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            if ($filtros['estado'] === 'todos') {
                // Mostrar todos incluyendo entregados
            } elseif ($filtros['estado'] === 'activos') {
                $sql .= " AND e.estado NOT IN ('entregado')";
            } else {
                $sql .= " AND e.estado = ?";
                $params[] = $filtros['estado'];
            }
        } else {
            $sql .= " AND e.estado NOT IN ('entregado')";
        }
        
        // Filtro por día
        if (!empty($filtros['dia'])) {
            $sql .= " AND DAY(at.fecha_asignacion) = ?";
            $params[] = $filtros['dia'];
        }
        
        // Filtro por mes
        if (!empty($filtros['mes'])) {
            $sql .= " AND MONTH(at.fecha_asignacion) = ?";
            $params[] = $filtros['mes'];
        }
        
        // Filtro por año
        if (!empty($filtros['anio'])) {
            $sql .= " AND YEAR(at.fecha_asignacion) = ?";
            $params[] = $filtros['anio'];
        }
        
        $sql .= " ORDER BY at.fecha_asignacion DESC";
        
        return $this->fetchAll($sql, $params);
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
    
    public function contarTrabajosNuevosParaTecnico($tecnico_id) {
        $sql = "SELECT COUNT(*) as total FROM asignaciones_tecnico at JOIN equipos e ON at.equipo_id = e.id WHERE at.tecnico_id = ? AND e.estado = 'asignado_sucursal' AND NOT EXISTS (SELECT 1 FROM seguimiento_trabajos st WHERE st.equipo_id = e.id AND st.tecnico_id = ? AND st.accion = 'recibido')";
        $result = $this->fetchOne($sql, [$tecnico_id, $tecnico_id]);
        return $result['total'] ?? 0;
    }
    
    public function contarTrabajosPendientesAsignarJefe($sucursal_id) {
        $sql = "SELECT COUNT(*) as total FROM equipos WHERE sucursal_actual_id = ? AND estado = 'asignado_sucursal' AND id NOT IN (SELECT equipo_id FROM asignaciones_tecnico)";
        $result = $this->fetchOne($sql, [$sucursal_id]);
        return $result['total'] ?? 0;
    }
    
    public function contarTrabajosCompletadosParaRecepcion($sucursal_id) {
        $sql = "SELECT COUNT(*) as total FROM equipos WHERE sucursal_actual_id = ? AND estado = 'completado'";
        $result = $this->fetchOne($sql, [$sucursal_id]);
        return $result['total'] ?? 0;
    }
    
    public function contarTrabajosNuevosParaAdmin($sucursal_id) {
        $sql = "SELECT COUNT(*) as total FROM equipos WHERE sucursal_origen_id = ? AND estado = 'pendiente_asignacion'";
        $result = $this->fetchOne($sql, [$sucursal_id]);
        return $result['total'] ?? 0;
    }
    
    public function obtenerEstadisticasPorTecnico($tecnico_id) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN e.estado IN ('recibido', 'en_reparacion') THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN e.estado = 'pausado' THEN 1 ELSE 0 END) as en_pausa,
                    SUM(CASE WHEN e.estado = 'completado' THEN 1 ELSE 0 END) as completados,
                    SUM(CASE WHEN e.estado = 'entregado' THEN 1 ELSE 0 END) as entregados
                FROM asignaciones_tecnico at
                JOIN equipos e ON at.equipo_id = e.id
                WHERE at.tecnico_id = ?";
        return $this->fetchOne($sql, [$tecnico_id]);
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
    
    public function obtenerCountPorFecha($fecha, $sucursal_id = null) {
        if ($sucursal_id) {
            $sql = "SELECT COUNT(*) as total FROM equipos WHERE DATE(fecha_registro) = ? AND sucursal_actual_id = ?";
            $result = $this->fetchOne($sql, [$fecha, $sucursal_id]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM equipos WHERE DATE(fecha_registro) = ?";
            $result = $this->fetchOne($sql, [$fecha]);
        }
        return $result['total'] ?? 0;
    }
    
    public function obtenerCompletadosPorSucursal($sucursal_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, c.dni as cliente_dni 
                FROM equipos e 
                JOIN clientes c ON e.cliente_id = c.id 
                WHERE e.sucursal_actual_id = ? AND e.estado = 'completado' 
                ORDER BY COALESCE(e.fecha_estimada_entrega, e.fecha_registro) ASC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerComponentesPorEquipo($equipo_id) {
        $sql = "SELECT sc.*, r.nombre as repuesto_nombre, r.codigo as repuesto_codigo
                FROM solicitudes_componentes sc
                JOIN repuestos r ON sc.repuesto_id = r.id
                WHERE sc.equipo_id = ? AND sc.estado = 'recibido'
                ORDER BY sc.fecha_solicitud ASC";
        return $this->fetchAll($sql, [$equipo_id]);
    }
    
    public function obtenerEntregadosPorSucursal($sucursal_id) {
        $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, c.dni as cliente_dni, CONCAT(u.nombre, ' ', u.apellido_paterno) as recepcionista_nombre FROM equipos e JOIN clientes c ON e.cliente_id = c.id LEFT JOIN usuarios u ON e.entregado_por = u.id WHERE e.sucursal_actual_id = ? AND e.estado = 'entregado' ORDER BY e.fecha_entrega DESC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerTodosConDetalles($filtros = []) {
        $sql = "SELECT e.*, 
                c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, c.dni as cliente_dni,
                CONCAT(r.nombre, ' ', r.apellido_paterno, ' ', r.apellido_materno) as recepcionista_nombre,
                s_actual.nombre as sucursal_actual_nombre,
                CONCAT(at2.nombre, ' ', at2.apellido_paterno) as tecnico_nombre,
                CONCAT(jt.nombre, ' ', jt.apellido_paterno) as jefe_tecnico_nombre,
                asig_tec.fecha_asignacion as fecha_asignacion_tecnico,
                CONCAT(ent.nombre, ' ', ent.apellido_paterno) as entregado_por_nombre,
                e.fecha_entrega
                FROM equipos e
                JOIN clientes c ON e.cliente_id = c.id
                LEFT JOIN usuarios r ON e.recepcionista_id = r.id
                LEFT JOIN sucursales s_actual ON e.sucursal_actual_id = s_actual.id
                LEFT JOIN asignaciones_tecnico asig_tec ON e.id = asig_tec.equipo_id
                LEFT JOIN usuarios at2 ON asig_tec.tecnico_id = at2.id
                LEFT JOIN usuarios jt ON asig_tec.jefe_tecnico_id = jt.id
                LEFT JOIN usuarios ent ON e.entregado_por = ent.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filtros['estado'])) {
            $where[] = "e.estado = ?";
            $params[] = $filtros['estado'];
        }
        if (!empty($filtros['sucursal_id'])) {
            $where[] = "e.sucursal_actual_id = ?";
            $params[] = $filtros['sucursal_id'];
        }
        if (!empty($filtros['busqueda'])) {
            $where[] = "(c.nombre LIKE ? OR c.apellido_paterno LIKE ? OR c.dni LIKE ? OR e.marca LIKE ? OR e.modelo LIKE ? OR e.numero_serie LIKE ?)";
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        if (!empty($filtros['fecha_desde'])) {
            $where[] = "DATE(e.fecha_registro) >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "DATE(e.fecha_registro) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY e.fecha_registro DESC";
        
        return $this->fetchAll($sql, $params);
    }
    
    public function obtenerTrazabilidadCompleta($equipo_id) {
        $sql = "SELECT e.*, 
                c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, c.dni as cliente_dni,
                CONCAT(r.nombre, ' ', r.apellido_paterno) as recepcionista_nombre,
                s_actual.nombre as sucursal_actual_nombre
                FROM equipos e
                JOIN clientes c ON e.cliente_id = c.id
                LEFT JOIN usuarios r ON e.recepcionista_id = r.id
                LEFT JOIN sucursales s_actual ON e.sucursal_actual_id = s_actual.id
                WHERE e.id = ?";
        return $this->fetchOne($sql, [$equipo_id]);
    }
    
    public function obtenerTimelineEquipo($equipo_id) {
        $timeline = [];
        
        $sql = "SELECT 'registro' as evento, e.fecha_registro as fecha, 
                CONCAT(r.nombre, ' ', r.apellido_paterno) as persona_nombre,
                'Recepcionista' as persona_rol,
                s.nombre as sucursal_nombre,
                'Equipo registrado en el sistema' as descripcion
                FROM equipos e
                LEFT JOIN usuarios r ON e.recepcionista_id = r.id
                LEFT JOIN sucursales s ON e.sucursal_origen_id = s.id
                WHERE e.id = ?";
        $registro = $this->fetchOne($sql, [$equipo_id]);
        if ($registro) {
            $timeline[] = $registro;
        }
        
        $sql = "SELECT 'asignacion_sucursal' as evento, asig.fecha_asignacion as fecha,
                CONCAT(u.nombre, ' ', u.apellido_paterno) as persona_nombre,
                'Admin. Sucursal' as persona_rol,
                CONCAT(s_dest.nombre, ' (desde ', s_orig.nombre, ')') as sucursal_nombre,
                asig.motivo as descripcion
                FROM asignaciones_sucursal asig
                LEFT JOIN usuarios u ON asig.admin_origen_id = u.id
                LEFT JOIN sucursales s_orig ON asig.sucursal_origen_id = s_orig.id
                LEFT JOIN sucursales s_dest ON asig.sucursal_destino_id = s_dest.id
                WHERE asig.equipo_id = ?
                ORDER BY asig.fecha_asignacion ASC";
        $asignaciones = $this->fetchAll($sql, [$equipo_id]);
        foreach ($asignaciones as $asig) {
            $timeline[] = $asig;
        }
        
        $sql = "SELECT CONCAT('asignacion_tecnico_', at2.id) as evento, at2.fecha_asignacion as fecha,
                CONCAT(jt.nombre, ' ', jt.apellido_paterno) as persona_nombre,
                'Jefe Técnico' as persona_rol,
                s.nombre as sucursal_nombre,
                CONCAT('Asignado a técnico: ', tec.nombre, ' ', tec.apellido_paterno) as descripcion
                FROM asignaciones_tecnico at2
                LEFT JOIN usuarios jt ON at2.jefe_tecnico_id = jt.id
                LEFT JOIN usuarios tec ON at2.tecnico_id = tec.id
                LEFT JOIN equipos e ON at2.equipo_id = e.id
                LEFT JOIN sucursales s ON e.sucursal_actual_id = s.id
                WHERE at2.equipo_id = ?
                ORDER BY at2.fecha_asignacion ASC";
        $asignaciones_tec = $this->fetchAll($sql, [$equipo_id]);
        foreach ($asignaciones_tec as $asig) {
            $timeline[] = $asig;
        }
        
        $sql = "SELECT st.accion as evento, st.fecha_registro as fecha,
                CONCAT(u.nombre, ' ', u.apellido_paterno) as persona_nombre,
                'Técnico' as persona_rol,
                s.nombre as sucursal_nombre,
                st.descripcion as descripcion
                FROM seguimiento_trabajos st
                LEFT JOIN usuarios u ON st.tecnico_id = u.id
                LEFT JOIN equipos e ON st.equipo_id = e.id
                LEFT JOIN sucursales s ON e.sucursal_actual_id = s.id
                WHERE st.equipo_id = ?
                ORDER BY st.fecha_registro ASC";
        $seguimientos = $this->fetchAll($sql, [$equipo_id]);
        foreach ($seguimientos as $seg) {
            $timeline[] = $seg;
        }
        
        $sql = "SELECT 'entrega' as evento, e.fecha_entrega as fecha,
                CONCAT(u.nombre, ' ', u.apellido_paterno) as persona_nombre,
                'Recepcionista' as persona_rol,
                s.nombre as sucursal_nombre,
                CONCAT('Equipo entregado al cliente. Costo: $', IFNULL(e.costo_final, 0)) as descripcion
                FROM equipos e
                LEFT JOIN usuarios u ON e.entregado_por = u.id
                LEFT JOIN sucursales s ON e.sucursal_actual_id = s.id
                WHERE e.id = ? AND e.estado = 'entregado' AND e.fecha_entrega IS NOT NULL";
        $entrega = $this->fetchOne($sql, [$equipo_id]);
        if ($entrega) {
            $timeline[] = $entrega;
        }
        
        usort($timeline, function($a, $b) {
            return strtotime($a['fecha']) - strtotime($b['fecha']);
        });
        
        return $timeline;
    }
}
