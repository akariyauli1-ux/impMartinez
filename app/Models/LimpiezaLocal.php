<?php
class LimpiezaLocal extends Model {
    protected $table = 'limpieza_local';
    
    public function registrar($data) {
        return $this->insert([
            'fecha' => $data['fecha'],
            'hora' => $data['hora'],
            'areas_limpiadas' => $data['areas_limpiadas'],
            'productos_utilizados' => $data['productos_utilizados'] ?? '',
            'observaciones' => $data['observaciones'] ?? '',
            'registrado_por' => $data['registrado_por'],
            'sucursal_id' => $data['sucursal_id']
        ]);
    }
    
    public function obtenerPorSucursal($sucursal_id) {
        $sql = "SELECT ll.*, CONCAT(u.nombre, ' ', u.apellido_paterno) as registrado_por_nombre FROM limpieza_local ll JOIN usuarios u ON ll.registrado_por = u.id WHERE ll.sucursal_id = ? ORDER BY ll.fecha DESC, ll.hora DESC";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
}
