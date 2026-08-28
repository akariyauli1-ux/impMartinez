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
        return $this->fetchAll("SELECT u.id, u.nombre, u.apellido_paterno, u.apellido_materno, COUNT(CASE WHEN e.estado NOT IN ('completado', 'entregado') THEN 1 END) as trabajos FROM usuarios u LEFT JOIN asignaciones_tecnico at ON u.id = at.tecnico_id LEFT JOIN equipos e ON at.equipo_id = e.id INNER JOIN usuario_roles ur ON u.id = ur.usuario_id INNER JOIN roles r ON ur.rol_id = r.id WHERE r.nombre = 'tecnico' AND u.sucursal_id = ? AND u.activo = 1 GROUP BY u.id ORDER BY u.apellido_paterno", [$sucursal_id]);
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
    
    public function obtenerAdminSucursal($sucursal_id) {
        return $this->fetchOne("SELECT * FROM v_usuarios_roles WHERE sucursal_id = ? AND FIND_IN_SET('admin_sucursal', roles) AND activo = 1 LIMIT 1", [$sucursal_id]);
    }
    
    // Métodos para roles múltiples
    
    public function obtenerRolesUsuario($usuario_id) {
        return $this->fetchAll("SELECT r.id, r.nombre, r.descripcion FROM usuario_roles ur JOIN roles r ON ur.rol_id = r.id WHERE ur.usuario_id = ? AND r.activo = 1 ORDER BY r.nombre", [$usuario_id]);
    }
    
    public function obtenerRolesIds($usuario_id) {
        $roles = $this->obtenerRolesUsuario($usuario_id);
        return array_column($roles, 'id');
    }
    
    public function tieneRol($usuario_id, $rol_nombre) {
        $result = $this->fetchOne("SELECT COUNT(*) as total FROM usuario_roles ur JOIN roles r ON ur.rol_id = r.id WHERE ur.usuario_id = ? AND r.nombre = ? AND r.activo = 1", [$usuario_id, $rol_nombre]);
        return ($result['total'] ?? 0) > 0;
    }
    
    public function asignarRol($usuario_id, $rol_id) {
        // Verificar si ya existe la asignación
        $existe = $this->fetchOne("SELECT id FROM usuario_roles WHERE usuario_id = ? AND rol_id = ?", [$usuario_id, $rol_id]);
        if ($existe) {
            return true; // Ya existe
        }
        return $this->query("INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (?, ?)", [$usuario_id, $rol_id]);
    }
    
    public function quitarRol($usuario_id, $rol_id) {
        return $this->query("DELETE FROM usuario_roles WHERE usuario_id = ? AND rol_id = ?", [$usuario_id, $rol_id]);
    }
    
    public function actualizarRoles($usuario_id, $roles_ids) {
        // Eliminar todos los roles actuales
        $this->query("DELETE FROM usuario_roles WHERE usuario_id = ?", [$usuario_id]);
        
        // Asignar los nuevos roles
        foreach ($roles_ids as $rol_id) {
            $this->asignarRol($usuario_id, $rol_id);
        }
        return true;
    }
    
    public function obtenerTodosRoles() {
        return $this->fetchAll("SELECT * FROM roles WHERE activo = 1 ORDER BY nombre");
    }
    
    public function obtenerUsuariosConRoles() {
        return $this->fetchAll("SELECT * FROM v_usuarios_roles ORDER BY activo DESC, apellido_paterno");
    }
}
