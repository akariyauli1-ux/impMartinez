<?php
class Asistencia extends Model {
    protected $table = 'asistencia';
    
    public function registrar($data) {
        $sql = "INSERT INTO {$this->table} 
                (usuario_id, fecha, hora_entrada, estado, registrado_por, estado_asistencia) 
                VALUES (?, ?, ?, ?, ?, 'pendiente')
                ON DUPLICATE KEY UPDATE 
                hora_entrada = VALUES(hora_entrada),
                estado = VALUES(estado),
                registrado_por = VALUES(registrado_por)";
        
        return $this->query($sql, [
            $data['usuario_id'],
            $data['fecha'],
            $data['hora_entrada'],
            $data['estado'] ?? 'presente',
            $data['registrado_por']
        ]);
    }
    
    public function obtenerPorUsuarioYFecha($usuario_id, $fecha) {
        $sql = "SELECT * FROM asistencia WHERE usuario_id = ? AND fecha = ?";
        return $this->fetchOne($sql, [$usuario_id, $fecha]);
    }
    
    public function actualizarSalida($id, $hora_salida) {
        return $this->update(['hora_salida' => $hora_salida], "id = ?", [$id]);
    }
    
    public function obtenerPendientesPorSucursal($sucursal_id, $fecha = null) {
        $sql = "SELECT a.*, 
                       CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as nombre_completo,
                       u.carnet,
                       (SELECT r.nombre FROM usuario_roles ur JOIN roles r ON ur.rol_id = r.id WHERE ur.usuario_id = u.id LIMIT 1) as rol_nombre
                FROM asistencia a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE u.sucursal_id = ? 
                AND a.estado_asistencia = 'pendiente'";
        $params = [$sucursal_id];
        
        if ($fecha) {
            $sql .= " AND a.fecha = ?";
            $params[] = $fecha;
        }
        
        $sql .= " ORDER BY a.fecha DESC, u.apellido_paterno";
        return $this->fetchAll($sql, $params);
    }
    
    public function obtenerHistorialPorSucursal($sucursal_id, $fecha = null) {
        $sql = "SELECT a.*, 
                       CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as nombre_completo,
                       u.carnet,
                       (SELECT r.nombre FROM usuario_roles ur JOIN roles r ON ur.rol_id = r.id WHERE ur.usuario_id = u.id LIMIT 1) as rol_nombre,
                       CONCAT(ap.nombre, ' ', ap.apellido_paterno) as aprobado_por_nombre
                FROM asistencia a
                JOIN usuarios u ON a.usuario_id = u.id
                LEFT JOIN usuarios ap ON a.aprobado_por = ap.id
                WHERE u.sucursal_id = ? 
                AND a.estado_asistencia != 'pendiente'";
        $params = [$sucursal_id];
        
        if ($fecha) {
            $sql .= " AND a.fecha = ?";
            $params[] = $fecha;
        }
        
        $sql .= " ORDER BY a.fecha DESC, u.apellido_paterno";
        return $this->fetchAll($sql, $params);
    }
    
    public function aprobar($id, $aprobado_por) {
        return $this->update([
            'estado_asistencia' => 'aprobado',
            'aprobado_por' => $aprobado_por,
            'fecha_aprobacion' => date('Y-m-d H:i:s')
        ], "id = ?", [$id]);
    }
    
    public function rechazar($id, $aprobado_por, $observaciones = '') {
        return $this->update([
            'estado_asistencia' => 'rechazado',
            'aprobado_por' => $aprobado_por,
            'fecha_aprobacion' => date('Y-m-d H:i:s'),
            'observaciones' => $observaciones
        ], "id = ?", [$id]);
    }
    
    public function obtenerResumenDelDia($sucursal_id, $fecha) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado_asistencia = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado_asistencia = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN estado_asistencia = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                FROM asistencia a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE u.sucursal_id = ? AND a.fecha = ?";
        return $this->fetchOne($sql, [$sucursal_id, $fecha]);
    }
}
