<?php
class Inspeccion extends Model {
    protected $table = 'inspecciones';
    
    public function registrar($data) {
        $sql = "INSERT INTO inspecciones (usuario_id, fecha, limpieza, uniforme, observaciones, registrado_por, hora_revision_limpieza, hora_revision_uniforme, obs_limpieza, obs_uniforme) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE limpieza=VALUES(limpieza), uniforme=VALUES(uniforme), observaciones=VALUES(observaciones), hora_revision_limpieza=VALUES(hora_revision_limpieza), hora_revision_uniforme=VALUES(hora_revision_uniforme), obs_limpieza=VALUES(obs_limpieza), obs_uniforme=VALUES(obs_uniforme)";
        return $this->query($sql, [
            $data['usuario_id'],
            $data['fecha'],
            $data['limpieza'],
            $data['uniforme'],
            $data['observaciones'] ?? '',
            $data['registrado_por'],
            $data['hora_limpieza'] ?? null,
            $data['hora_uniforme'] ?? null,
            $data['obs_limpieza'] ?? '',
            $data['obs_uniforme'] ?? ''
        ]);
    }
    
    public function obtenerPorFechaYSucursal($fecha, $sucursal_id) {
        $sql = "SELECT u.id as usuario_id, u.nombre, u.apellido_paterno, u.rol, i.limpieza, i.uniforme, i.observaciones, i.hora_revision_limpieza, i.hora_revision_uniforme, i.obs_limpieza, i.obs_uniforme FROM usuarios u LEFT JOIN inspecciones i ON u.id = i.usuario_id AND i.fecha = ? WHERE u.sucursal_id = ? AND u.activo = 1 AND u.rol IN ('tecnico', 'recepcionista', 'almacenista', 'jefe_tecnico') ORDER BY u.apellido_paterno";
        return $this->fetchAll($sql, [$fecha, $sucursal_id]);
    }
    
    public function obtenerReporte($fecha, $sucursal_id = null) {
        if ($sucursal_id) {
            $sql = "SELECT i.*, u.nombre, u.apellido_paterno, u.rol, s.nombre as sucursal_nombre, CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre FROM inspecciones i JOIN usuarios u ON i.usuario_id = u.id LEFT JOIN sucursales s ON u.sucursal_id = s.id LEFT JOIN usuarios reg ON i.registrado_por = reg.id WHERE i.fecha = ? AND u.sucursal_id = ? ORDER BY s.nombre, u.apellido_paterno";
            return $this->fetchAll($sql, [$fecha, $sucursal_id]);
        }
        $sql = "SELECT i.*, u.nombre, u.apellido_paterno, u.rol, s.nombre as sucursal_nombre, CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre FROM inspecciones i JOIN usuarios u ON i.usuario_id = u.id LEFT JOIN sucursales s ON u.sucursal_id = s.id LEFT JOIN usuarios reg ON i.registrado_por = reg.id WHERE i.fecha = ? ORDER BY s.nombre, u.apellido_paterno";
        return $this->fetchAll($sql, [$fecha]);
    }
    
    public function obtenerHistorialPorSucursal($sucursal_id, $fecha_inicio = null, $fecha_fin = null) {
        $params = [$sucursal_id];
        $where = "WHERE u.sucursal_id = ?";
        
        if ($fecha_inicio) {
            $where .= " AND i.fecha >= ?";
            $params[] = $fecha_inicio;
        }
        if ($fecha_fin) {
            $where .= " AND i.fecha <= ?";
            $params[] = $fecha_fin;
        }
        
        $sql = "SELECT i.*, u.nombre, u.apellido_paterno, u.rol, CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre FROM inspecciones i JOIN usuarios u ON i.usuario_id = u.id LEFT JOIN usuarios reg ON i.registrado_por = reg.id $where ORDER BY i.fecha DESC, u.apellido_paterno";
        return $this->fetchAll($sql, $params);
    }
}
