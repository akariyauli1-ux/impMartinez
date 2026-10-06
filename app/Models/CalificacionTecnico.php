<?php
class CalificacionTecnico extends Model {
    protected $table = 'calificaciones_tecnicos';
    
    public function guardar($data) {
        $sql = "INSERT INTO {$this->table} 
                (tecnico_id, jefe_tecnico_id, mes, anio, puntuacion_trabajo, puntuacion_asistencia, puntuacion_total, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                puntuacion_trabajo = VALUES(puntuacion_trabajo),
                puntuacion_asistencia = VALUES(puntuacion_asistencia),
                puntuacion_total = VALUES(puntuacion_total),
                observaciones = VALUES(observaciones),
                fecha_calificacion = CURRENT_TIMESTAMP";
        
        return $this->query($sql, [
            $data['tecnico_id'],
            $data['jefe_tecnico_id'],
            $data['mes'],
            $data['anio'],
            $data['puntuacion_trabajo'],
            $data['puntuacion_asistencia'],
            $data['puntuacion_total'],
            $data['observaciones'] ?? null
        ]);
    }
    
    public function obtenerPorTecnicoYMes($tecnico_id, $mes, $anio) {
        $sql = "SELECT c.*, 
                       CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as tecnico_nombre,
                       CONCAT(j.nombre, ' ', j.apellido_paterno) as jefe_nombre
                FROM {$this->table} c
                JOIN usuarios u ON c.tecnico_id = u.id
                JOIN usuarios j ON c.jefe_tecnico_id = j.id
                WHERE c.tecnico_id = ? AND c.mes = ? AND c.anio = ?";
        return $this->fetchOne($sql, [$tecnico_id, $mes, $anio]);
    }
    
    public function obtenerRankingPorMes($sucursal_id, $mes, $anio) {
        $sql = "SELECT c.*, 
                       CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as tecnico_nombre,
                       u.carnet,
                       (SELECT r.nombre FROM usuario_roles ur JOIN roles r ON ur.rol_id = r.id WHERE ur.usuario_id = u.id LIMIT 1) as rol_nombre
                FROM {$this->table} c
                JOIN usuarios u ON c.tecnico_id = u.id
                WHERE u.sucursal_id = ? AND c.mes = ? AND c.anio = ?
                ORDER BY c.puntuacion_total DESC";
        return $this->fetchAll($sql, [$sucursal_id, $mes, $anio]);
    }
    
    public function obtenerAsistenciasAprobadasPorTecnicoYMes($tecnico_id, $mes, $anio) {
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) as presentes,
                       SUM(CASE WHEN estado = 'tardanza' THEN 1 ELSE 0 END) as tardanzas
                FROM asistencia
                WHERE usuario_id = ? 
                AND MONTH(fecha) = ? 
                AND YEAR(fecha) = ?
                AND estado_asistencia = 'aprobado'";
        return $this->fetchOne($sql, [$tecnico_id, $mes, $anio]);
    }
    
    public function obtenerTecnicosPorSucursal($sucursal_id) {
        $sql = "SELECT u.id, 
                       CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as nombre_completo,
                       u.carnet,
                       (SELECT r.nombre FROM usuario_roles ur JOIN roles r ON ur.rol_id = r.id WHERE ur.usuario_id = u.id LIMIT 1) as rol_nombre
                FROM usuarios u
                WHERE u.sucursal_id = ? 
                AND EXISTS (
                    SELECT 1 FROM usuario_roles ur 
                    JOIN roles r ON ur.rol_id = r.id 
                    WHERE ur.usuario_id = u.id 
                    AND r.nombre = 'tecnico'
                )
                AND u.activo = 1
                ORDER BY u.apellido_paterno";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
}
