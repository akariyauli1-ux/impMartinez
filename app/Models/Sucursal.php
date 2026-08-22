<?php
class Sucursal extends Model {
    protected $table = 'sucursales';
    
    public function obtenerTodas() {
        return $this->fetchAll("SELECT * FROM sucursales WHERE activo = 1 ORDER BY nombre");
    }
    
    public function obtenerPorId($id) {
        return $this->fetchOne("SELECT * FROM sucursales WHERE id = ?", [$id]);
    }
    
    public function obtenerLogoEmpresa() {
        $result = $this->fetchOne("SELECT logo_empresa FROM sucursales WHERE id = 1");
        return $result['logo_empresa'] ?? null;
    }
    
    public function obtenerResumen() {
        return $this->fetchAll("
            SELECT s.*, 
                   COUNT(DISTINCT e.id) as total_equipos,
                   SUM(CASE WHEN e.estado = 'en_reparacion' THEN 1 ELSE 0 END) as en_reparacion,
                   SUM(CASE WHEN e.estado IN ('completado', 'entregado') THEN 1 ELSE 0 END) as completados,
                   SUM(CASE WHEN e.estado IN ('registrado', 'pendiente_asignacion') THEN 1 ELSE 0 END) as pendientes,
                   COUNT(DISTINCT u.id) as total_personal
            FROM sucursales s
            LEFT JOIN equipos e ON e.sucursal_actual_id = s.id
            LEFT JOIN usuarios u ON u.sucursal_id = s.id AND u.activo = 1
            WHERE s.activo = 1
            GROUP BY s.id
            ORDER BY s.nombre
        ");
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
    
    public function actualizarLogoEmpresa($logo) {
        return $this->update(['logo_empresa' => $logo], "id = 1");
    }
}
