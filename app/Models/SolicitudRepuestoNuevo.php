<?php
class SolicitudRepuestoNuevo extends Model {
    protected $table = 'solicitudes_repuestos_nuevos';
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function obtenerPorTecnico($tecnico_id) {
        $sql = "SELECT srn.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo
                FROM solicitudes_repuestos_nuevos srn
                JOIN equipos e ON srn.equipo_id = e.id
                JOIN clientes c ON e.cliente_id = c.id
                LEFT JOIN repuestos r ON srn.repuesto_id = r.id
                WHERE srn.tecnico_id = ?
                ORDER BY srn.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$tecnico_id]);
    }
    
    public function obtenerTodas() {
        $sql = "SELECT srn.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_ap,
                       r.nombre as repuesto_nombre, r.codigo as repuesto_codigo,
                       p.nombre as procesado_por_nombre
                FROM solicitudes_repuestos_nuevos srn
                JOIN equipos e ON srn.equipo_id = e.id
                JOIN clientes c ON e.cliente_id = c.id
                JOIN usuarios t ON srn.tecnico_id = t.id
                LEFT JOIN repuestos r ON srn.repuesto_id = r.id
                LEFT JOIN usuarios p ON srn.procesado_por = p.id
                ORDER BY srn.fecha_solicitud DESC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerPendientes() {
        $sql = "SELECT srn.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_ap
                FROM solicitudes_repuestos_nuevos srn
                JOIN equipos e ON srn.equipo_id = e.id
                JOIN clientes c ON e.cliente_id = c.id
                JOIN usuarios t ON srn.tecnico_id = t.id
                WHERE srn.estado = 'pendiente'
                ORDER BY srn.fecha_solicitud ASC";
        return $this->fetchAll($sql);
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT srn.*, 
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
                       t.nombre as tecnico_nombre, t.apellido_paterno as tecnico_ap
                FROM solicitudes_repuestos_nuevos srn
                JOIN equipos e ON srn.equipo_id = e.id
                JOIN clientes c ON e.cliente_id = c.id
                JOIN usuarios t ON srn.tecnico_id = t.id
                WHERE srn.id = ?";
        return $this->fetchOne($sql, [$id]);
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
    
    public function marcarComoCreado($id, $repuesto_id, $precio_unitario, $procesado_por) {
        $data = [
            'estado' => 'creado',
            'repuesto_id' => $repuesto_id,
            'precio_unitario' => $precio_unitario,
            'procesado_por' => $procesado_por,
            'fecha_procesado' => date('Y-m-d H:i:s')
        ];
        return $this->actualizar($id, $data);
    }
    
    public function marcarComoCompradoExterno($id, $precio_unitario, $proveedor, $procesado_por) {
        $data = [
            'estado' => 'comprado_externo',
            'precio_unitario' => $precio_unitario,
            'proveedor' => $proveedor,
            'procesado_por' => $procesado_por,
            'fecha_procesado' => date('Y-m-d H:i:s')
        ];
        return $this->actualizar($id, $data);
    }
    
    public function agregarAlCostoReparacion($equipo_id, $precio) {
        $sql = "UPDATE equipos SET costo_reparacion = COALESCE(costo_reparacion, 0) + ? WHERE id = ?";
        return $this->query($sql, [$precio, $equipo_id]);
    }
    
    public function obtenerComprasExternasPendientesTecnico($tecnico_id) {
        $sql = "SELECT ce.*, srn.nombre_repuesto, srn.marca as marca_repuesto, srn.descripcion,
                       e.tipo_equipo, e.marca as equipo_marca, e.modelo as equipo_modelo,
                       c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap
                FROM compras_externas ce
                JOIN solicitudes_repuestos_nuevos srn ON ce.solicitud_repuesto_nuevo_id = srn.id
                JOIN equipos e ON ce.equipo_id = e.id
                JOIN clientes c ON e.cliente_id = c.id
                WHERE ce.tecnico_id = ? AND ce.estado = 'recibida' AND srn.estado = 'enviado'
                ORDER BY ce.fecha_solicitud DESC";
        return $this->fetchAll($sql, [$tecnico_id]);
    }
    
    public function confirmarRecibidoTecnico($compra_id, $tecnico_id) {
        $sql = "UPDATE solicitudes_repuestos_nuevos SET estado = 'recibido' 
                WHERE compra_externa_id = ? AND tecnico_id = ? AND estado = 'enviado'";
        $this->query($sql, [$compra_id, $tecnico_id]);
        return true;
    }
}
