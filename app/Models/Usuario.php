<?php
class Usuario extends Model {
    protected $table = 'usuarios';
    
    public function obtenerPorId($id) {
        return $this->fetchOne("SELECT u.*, s.nombre as sucursal_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id WHERE u.id = ?", [$id]);
    }
    
    public function obtenerFoto($id) {
        return $this->fetchOne("SELECT foto_data, foto_tipo FROM usuarios WHERE id = ?", [$id]);
    }
    
    public function actualizarFoto($id, $foto_nombre, $foto_data, $foto_tipo) {
        return $this->update(['foto' => $foto_nombre, 'foto_data' => $foto_data, 'foto_tipo' => $foto_tipo], "id = ?", [$id]);
    }
    
    public function obtenerPorApellidoYCarnet($apellido, $carnet) {
        return $this->fetchOne("SELECT id, nombre, apellido_paterno, apellido_materno, password, rol, sucursal_id FROM usuarios WHERE (apellido_paterno = ? OR apellido_materno = ?) AND carnet = ? AND activo = 1", [$apellido, $apellido, $carnet]);
    }
    
    public function obtenerTodosPorSucursal($sucursal_id) {
        return $this->fetchAll("SELECT * FROM usuarios WHERE sucursal_id = ? AND activo = 1 ORDER BY apellido_paterno", [$sucursal_id]);
    }
    
    public function obtenerTodos() {
        return $this->fetchAll("SELECT u.*, s.nombre as sucursal_nombre, CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id LEFT JOIN usuarios reg ON u.registrado_por = reg.id ORDER BY u.activo DESC, u.apellido_paterno");
    }
    
    public function obtenerTodosIncluyendoInactivos() {
        return $this->fetchAll("SELECT u.*, s.nombre as sucursal_nombre, CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id LEFT JOIN usuarios reg ON u.registrado_por = reg.id ORDER BY u.activo DESC, u.apellido_paterno");
    }
    
    public function obtenerTecnicosPorSucursal($sucursal_id) {
        return $this->fetchAll("SELECT u.id, u.nombre, u.apellido_paterno, u.apellido_materno, COUNT(CASE WHEN e.estado NOT IN ('completado', 'entregado') THEN 1 END) as trabajos FROM usuarios u LEFT JOIN asignaciones_tecnico at ON u.id = at.tecnico_id LEFT JOIN equipos e ON at.equipo_id = e.id WHERE u.rol = 'tecnico' AND u.sucursal_id = ? AND u.activo = 1 GROUP BY u.id ORDER BY u.apellido_paterno", [$sucursal_id]);
    }
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
    
    public function activar($id) {
        return $this->update(['activo' => 1], "id = ?", [$id]);
    }
    
    public function desactivar($id) {
        return $this->update(['activo' => 0], "id = ?", [$id]);
    }
    
    public function toggleEstado($id) {
        $usuario = $this->obtenerPorId($id);
        if ($usuario['activo']) {
            return $this->desactivar($id);
        } else {
            return $this->activar($id);
        }
    }
}
