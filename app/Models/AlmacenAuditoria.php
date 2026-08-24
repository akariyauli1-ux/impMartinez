<?php
class AlmacenAuditoria extends Model {
    protected $table = 'almacen_auditoria';
    
    public function registrar($usuario_id, $accion, $tabla_afectada, $registro_id, $descripcion, $datos_antiguos = null, $datos_nuevos = null) {
        $data = [
            'usuario_id' => $usuario_id,
            'accion' => $accion,
            'tabla_afectada' => $tabla_afectada,
            'registro_id' => $registro_id,
            'descripcion' => $descripcion,
            'datos_antiguos' => $datos_antiguos ? json_encode($datos_antiguos) : null,
            'datos_nuevos' => $datos_nuevos ? json_encode($datos_nuevos) : null
        ];
        return $this->insert($data);
    }
    
    public function obtenerHistorial($limite = 100) {
        $sql = "SELECT a.*, u.nombre, u.apellido_paterno, u.apellido_materno 
                FROM almacen_auditoria a 
                JOIN usuarios u ON a.usuario_id = u.id 
                ORDER BY a.fecha DESC 
                LIMIT ?";
        return $this->fetchAll($sql, [$limite]);
    }
    
    public function obtenerHistorialPorUsuario($usuario_id, $limite = 50) {
        $sql = "SELECT a.*, u.nombre, u.apellido_paterno 
                FROM almacen_auditoria a 
                JOIN usuarios u ON a.usuario_id = u.id 
                WHERE a.usuario_id = ?
                ORDER BY a.fecha DESC 
                LIMIT ?";
        return $this->fetchAll($sql, [$usuario_id, $limite]);
    }
    
    public function obtenerHistorialPorAccion($accion, $limite = 50) {
        $sql = "SELECT a.*, u.nombre, u.apellido_paterno 
                FROM almacen_auditoria a 
                JOIN usuarios u ON a.usuario_id = u.id 
                WHERE a.accion = ?
                ORDER BY a.fecha DESC 
                LIMIT ?";
        return $this->fetchAll($sql, [$accion, $limite]);
    }
}
