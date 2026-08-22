<?php
class SeguimientoTrabajo extends Model {
    protected $table = 'seguimiento_trabajos';
    
    public function registrar($equipo_id, $tecnico_id, $accion, $descripcion = '') {
        return $this->insert([
            'equipo_id' => $equipo_id,
            'tecnico_id' => $tecnico_id,
            'accion' => $accion,
            'descripcion' => $descripcion
        ]);
    }
    
    public function obtenerPorEquipo($equipo_id) {
        $sql = "SELECT * FROM seguimiento_trabajos WHERE equipo_id = ? ORDER BY fecha_registro ASC";
        return $this->fetchAll($sql, [$equipo_id]);
    }
}
