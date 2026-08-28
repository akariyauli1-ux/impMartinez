<?php
class Cliente extends Model {
    protected $table = 'clientes';
    
    public function obtenerTodos() {
        return $this->fetchAll("SELECT id, CONCAT(nombre, ' ', apellido_paterno, ' ', IFNULL(apellido_materno,'')) as nombre_completo, telefono, dni, email, direccion FROM clientes ORDER BY nombre ASC");
    }
    
    public function obtenerPorId($id) {
        return $this->fetchOne("SELECT * FROM clientes WHERE id = ?", [$id]);
    }
    
    public function crear($data) {
        return $this->insert($data);
    }
}
