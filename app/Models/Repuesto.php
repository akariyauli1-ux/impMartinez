<?php
class Repuesto extends Model {
    protected $table = 'repuestos';
    
    public function obtenerPorSucursal($sucursal_id) {
        $sql = "SELECT * FROM repuestos WHERE sucursal_id = ? ORDER BY categoria, marca, nombre";
        return $this->fetchAll($sql, [$sucursal_id]);
    }
    
    public function obtenerTodos() {
        $sql = "SELECT r.*, s.nombre as sucursal_nombre FROM repuestos r LEFT JOIN sucursales s ON r.sucursal_id = s.id ORDER BY r.categoria, r.marca, r.nombre";
        return $this->fetchAll($sql);
    }
    
    public function obtenerPorId($id) {
        return $this->fetchOne("SELECT * FROM repuestos WHERE id = ?", [$id]);
    }
    
    public function crear($data) {
        return $this->insert($data);
    }
    
    public function actualizar($id, $data) {
        return $this->update($data, "id = ?", [$id]);
    }
    
    public function obtenerStockBajo($sucursal_id = null) {
        if ($sucursal_id) {
            $sql = "SELECT COUNT(*) as total FROM repuestos WHERE sucursal_id = ? AND stock <= stock_minimo AND descontinuado = 0";
            $result = $this->fetchOne($sql, [$sucursal_id]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM repuestos WHERE stock <= stock_minimo AND descontinuado = 0";
            $result = $this->fetchOne($sql);
        }
        return $result['total'] ?? 0;
    }
    
    public function obtenerCategorias() {
        $sql = "SELECT DISTINCT categoria FROM repuestos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria";
        return $this->fetchAll($sql);
    }
    
    public function obtenerMarcas() {
        $sql = "SELECT DISTINCT marca FROM repuestos WHERE marca IS NOT NULL AND marca != '' ORDER BY marca";
        return $this->fetchAll($sql);
    }
    
    public function obtenerMasSolicitados($limite = 10) {
        $sql = "SELECT r.id, r.nombre, r.codigo, r.marca, r.categoria, r.solicitudes, r.ventas, s.nombre as sucursal_nombre FROM repuestos r LEFT JOIN sucursales s ON r.sucursal_id = s.id ORDER BY r.solicitudes DESC, r.ventas DESC LIMIT ?";
        return $this->fetchAll($sql, [$limite]);
    }
    
    public function obtenerPedidosPorSucursal() {
        $sql = "SELECT s.id, s.nombre, COUNT(p.id) as total_pedidos, SUM(p.cantidad) as total_unidades, COALESCE(SUM(p.total), 0) as total_monto FROM sucursales s LEFT JOIN pedidos_repuestos p ON s.id = p.sucursal_id GROUP BY s.id, s.nombre ORDER BY total_pedidos DESC";
        return $this->fetchAll($sql);
    }
    
    public function actualizarStock($id, $cantidad, $tipo = 'resta') {
        $repuesto = $this->obtenerPorId($id);
        if (!$repuesto) return false;
        
        $nuevo_stock = $tipo === 'suma' ? $repuesto['stock'] + $cantidad : max(0, $repuesto['stock'] - $cantidad);
        $nuevas_unidades = $tipo === 'suma' ? $repuesto['unidades_disponibles'] + $cantidad : max(0, $repuesto['unidades_disponibles'] - $cantidad);
        
        return $this->update([
            'stock' => $nuevo_stock,
            'unidades_disponibles' => $nuevas_unidades
        ], "id = ?", [$id]);
    }
    
    public function incrementarSolicitudes($id, $cantidad = 1) {
        $repuesto = $this->obtenerPorId($id);
        if (!$repuesto) return false;
        return $this->update(['solicitudes' => $repuesto['solicitudes'] + $cantidad], "id = ?", [$id]);
    }
    
    public function incrementarVentas($id, $cantidad = 1, $precio_total = 0) {
        $repuesto = $this->obtenerPorId($id);
        if (!$repuesto) return false;
        return $this->update([
            'ventas' => $repuesto['ventas'] + $cantidad,
            'movimiento_salida' => $repuesto['movimiento_salida'] + $cantidad,
            'inversion' => $repuesto['inversion'] + $precio_total
        ], "id = ?", [$id]);
    }
    
    public function contarPorCategoria($categoria, $sucursal_id = null) {
        if ($sucursal_id) {
            $sql = "SELECT COUNT(*) as total FROM repuestos WHERE categoria = ? AND sucursal_id = ?";
            $result = $this->fetchOne($sql, [$categoria, $sucursal_id]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM repuestos WHERE categoria = ?";
            $result = $this->fetchOne($sql, [$categoria]);
        }
        return $result['total'] ?? 0;
    }
    
    public function editarCategoria($categoria_actual, $categoria_nueva, $sucursal_id = null) {
        if ($sucursal_id) {
            return $this->update(['categoria' => $categoria_nueva], "categoria = ? AND sucursal_id = ?", [$categoria_actual, $sucursal_id]);
        } else {
            return $this->update(['categoria' => $categoria_nueva], "categoria = ?", [$categoria_actual]);
        }
    }
    
    public function eliminarCategoria($categoria, $sucursal_id = null) {
        if ($sucursal_id) {
            return $this->update(['categoria' => ''], "categoria = ? AND sucursal_id = ?", [$categoria, $sucursal_id]);
        } else {
            return $this->update(['categoria' => ''], "categoria = ?", [$categoria]);
        }
    }
    
    public function descontarStockReservado($id, $cantidad) {
        $repuesto = $this->obtenerPorId($id);
        if (!$repuesto) return false;
        
        $nuevo_stock_reservado = ($repuesto['stock_reservado'] ?? 0) + $cantidad;
        $unidades_reales = max(0, ($repuesto['unidades_disponibles'] ?? 0) - $cantidad);
        
        return $this->update([
            'stock_reservado' => $nuevo_stock_reservado,
            'unidades_disponibles' => $unidades_reales
        ], "id = ?", [$id]);
    }
    
    public function confirmarSalidaInventario($repuesto_id, $cantidad, $usuario_id) {
        $repuesto = $this->obtenerPorId($repuesto_id);
        if (!$repuesto) return false;
        
        $nuevo_stock = max(0, ($repuesto['stock'] ?? 0) - $cantidad);
        $nuevo_stock_reservado = max(0, ($repuesto['stock_reservado'] ?? 0) - $cantidad);
        $precio_total = ($repuesto['precio_unitario'] ?? 0) * $cantidad;
        
        return $this->update([
            'stock' => $nuevo_stock,
            'stock_reservado' => $nuevo_stock_reservado,
            'movimiento_salida' => ($repuesto['movimiento_salida'] ?? 0) + $cantidad,
            'solicitudes' => ($repuesto['solicitudes'] ?? 0) + $cantidad,
            'ventas' => ($repuesto['ventas'] ?? 0) + $cantidad,
            'inversion' => ($repuesto['inversion'] ?? 0) + $precio_total
        ], "id = ?", [$repuesto_id]);
    }
    
    public function devolverStockReservado($id, $cantidad) {
        $repuesto = $this->obtenerPorId($id);
        if (!$repuesto) return false;
        
        $nuevo_stock_reservado = max(0, ($repuesto['stock_reservado'] ?? 0) - $cantidad);
        $unidades_reales = ($repuesto['unidades_disponibles'] ?? 0) + $cantidad;
        
        return $this->update([
            'stock_reservado' => $nuevo_stock_reservado,
            'unidades_disponibles' => $unidades_reales
        ], "id = ?", [$id]);
    }
}
