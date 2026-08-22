<?php
class AsignacionSucursal extends Model {
    protected $table = 'asignaciones_sucursal';
    
    public function asignar($equipo_id, $sucursal_origen_id, $sucursal_destino_id, $admin_origen_id, $motivo = '') {
        return $this->insert([
            'equipo_id' => $equipo_id,
            'sucursal_origen_id' => $sucursal_origen_id,
            'sucursal_destino_id' => $sucursal_destino_id,
            'admin_origen_id' => $admin_origen_id,
            'motivo' => $motivo
        ]);
    }
}
