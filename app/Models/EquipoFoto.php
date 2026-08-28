<?php
class EquipoFoto extends Model {
    protected $table = 'equipos_fotos';
    
    public function guardar($equipo_id, $foto_data, $foto_tipo, $orden = 0, $tipo = 'general') {
        return $this->insert([
            'equipo_id' => $equipo_id,
            'foto_data' => $foto_data,
            'foto_tipo' => $foto_tipo,
            'orden' => $orden,
            'tipo' => $tipo
        ]);
    }
    
    public function obtenerPorEquipo($equipo_id) {
        return $this->fetchAll("SELECT id, foto_tipo, orden, tipo FROM equipos_fotos WHERE equipo_id = ? ORDER BY orden", [$equipo_id]);
    }
    
    public function obtenerFoto($id) {
        return $this->fetchOne("SELECT foto_data, foto_tipo FROM equipos_fotos WHERE id = ?", [$id]);
    }
    
    public function eliminarPorEquipo($equipo_id) {
        return $this->delete("equipo_id = ?", [$equipo_id]);
    }
}
