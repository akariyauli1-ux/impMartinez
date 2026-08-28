<?php
class AsignacionTecnico extends Model {
    protected $table = 'asignaciones_tecnico';
    
    public function asignar($equipo_id, $tecnico_id, $jefe_tecnico_id) {
        return $this->insert([
            'equipo_id' => $equipo_id,
            'tecnico_id' => $tecnico_id,
            'jefe_tecnico_id' => $jefe_tecnico_id
        ]);
    }
    
    public function contarTrabajosActivos($tecnico_id) {
        $sql = "SELECT COUNT(*) as total FROM asignaciones_tecnico at JOIN equipos e ON at.equipo_id = e.id WHERE at.tecnico_id = ? AND e.estado NOT IN ('completado', 'entregado', 'asignado_sucursal')";
        $result = $this->fetchOne($sql, [$tecnico_id]);
        return $result['total'] ?? 0;
    }
    
    public function obtenerPorSucursal($sucursal_id) {
        $sql = "SELECT at.equipo_id, at.tecnico_id, at.fecha_asignacion,
                e.estado as equipo_estado,
                e.tipo_equipo as equipo_tipo,
                e.marca as equipo_marca,
                e.modelo as equipo_modelo,
                c.nombre as cliente_nombre,
                c.apellido_paterno as cliente_apellido,
                u.nombre as tecnico_nombre,
                u.apellido_paterno as tecnico_apellido
                FROM asignaciones_tecnico at 
                JOIN equipos e ON at.equipo_id = e.id 
                JOIN usuarios u ON at.tecnico_id = u.id
                JOIN clientes c ON e.cliente_id = c.id
                WHERE e.sucursal_actual_id = ? 
                ORDER BY at.fecha_asignacion DESC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function eliminarPorEquipo($equipo_id) {
        return $this->query("DELETE FROM asignaciones_tecnico WHERE equipo_id = ?", [$equipo_id]);
    }
}
