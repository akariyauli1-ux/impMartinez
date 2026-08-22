<?php
class Asistencia extends Model {
    protected $table = 'asistencia';
    
    public function registrar($data) {
        $sql = "INSERT INTO asistencia (usuario_id, fecha, hora_entrada, hora_salida, estado, observaciones, registrado_por) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE hora_entrada=VALUES(hora_entrada), hora_salida=VALUES(hora_salida), estado=VALUES(estado), observaciones=VALUES(observaciones)";
        return $this->query($sql, [
            $data['usuario_id'],
            $data['fecha'],
            $data['hora_entrada'] ?? null,
            $data['hora_salida'] ?? null,
            $data['estado'],
            $data['observaciones'] ?? '',
            $data['registrado_por']
        ]);
    }
    
    public function obtenerPorFechaYSucursal($fecha, $sucursal_id) {
        $sql = "SELECT a.*, u.nombre, u.apellido_paterno, u.rol FROM asistencia a JOIN usuarios u ON a.usuario_id = u.id WHERE a.fecha = ? AND u.sucursal_id = ? AND u.activo = 1 AND u.rol IN ('tecnico', 'recepcionista', 'almacenista', 'jefe_tecnico') ORDER BY u.apellido_paterno";
        return $this->fetchAll($sql, [$fecha, $sucursal_id]);
    }
    
    public function obtenerReporte($fecha, $sucursal_id = null) {
        if ($sucursal_id) {
            $sql = "SELECT a.*, u.nombre, u.apellido_paterno, u.rol, s.nombre as sucursal_nombre, CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre FROM asistencia a JOIN usuarios u ON a.usuario_id = u.id LEFT JOIN sucursales s ON u.sucursal_id = s.id LEFT JOIN usuarios reg ON a.registrado_por = reg.id WHERE a.fecha = ? AND u.sucursal_id = ? ORDER BY s.nombre, u.apellido_paterno";
            return $this->fetchAll($sql, [$fecha, $sucursal_id]);
        }
        $sql = "SELECT a.*, u.nombre, u.apellido_paterno, u.rol, s.nombre as sucursal_nombre, CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre FROM asistencia a JOIN usuarios u ON a.usuario_id = u.id LEFT JOIN sucursales s ON u.sucursal_id = s.id LEFT JOIN usuarios reg ON a.registrado_por = reg.id WHERE a.fecha = ? ORDER BY s.nombre, u.apellido_paterno";
        return $this->fetchAll($sql, [$fecha]);
    }
    
    public function obtenerCountPorFecha($fecha, $sucursal_id = null) {
        if ($sucursal_id) {
            $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) as presentes, SUM(CASE WHEN estado = 'tardanza' THEN 1 ELSE 0 END) as tardanzas, SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) as ausentes FROM asistencia WHERE fecha = ? AND usuario_id IN (SELECT id FROM usuarios WHERE sucursal_id = ?)";
            return $this->fetchOne($sql, [$fecha, $sucursal_id]);
        }
        $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) as presentes, SUM(CASE WHEN estado = 'tardanza' THEN 1 ELSE 0 END) as tardanzas, SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) as ausentes FROM asistencia WHERE fecha = ?";
        return $this->fetchOne($sql, [$fecha]);
    }
}
