<?php
class LoteInventario extends Model {
    protected $table = 'lotes_inventario';
    
    public function registrarEntrada($repuesto_id, $cantidad, $precio_compra, $almacenista_id, $motivo = null) {
        $data = [
            'repuesto_id' => $repuesto_id,
            'cantidad' => $cantidad,
            'cantidad_restante' => $cantidad,
            'precio_compra' => $precio_compra,
            'almacenista_id' => $almacenista_id,
            'motivo' => $motivo
        ];
        return $this->insert($data);
    }
    
    public function obtenerLotesPorRepuesto($repuesto_id) {
        $sql = "SELECT l.*, u.nombre as almacenista_nombre FROM lotes_inventario l JOIN usuarios u ON l.almacenista_id = u.id WHERE l.repuesto_id = ? AND l.cantidad_restante > 0 ORDER BY l.fecha_entrada ASC";
        return $this->fetchAll($sql, [$repuesto_id]);
    }
    
    public function obtenerTodosLotesPorRepuesto($repuesto_id) {
        $sql = "SELECT l.*, u.nombre as almacenista_nombre FROM lotes_inventario l JOIN usuarios u ON l.almacenista_id = u.id WHERE l.repuesto_id = ? ORDER BY l.fecha_entrada ASC";
        return $this->fetchAll($sql, [$repuesto_id]);
    }
    
    public function consumirFIFO($repuesto_id, $cantidad) {
        $lotes = $this->obtenerLotesPorRepuesto($repuesto_id);
        
        if (empty($lotes)) return false;
        
        $total_disponible = array_sum(array_column($lotes, 'cantidad_restante'));
        if ($total_disponible < $cantidad) return false;
        
        $cantidad_por_consumir = $cantidad;
        $costo_total = 0;
        
        foreach ($lotes as $lote) {
            if ($cantidad_por_consumir <= 0) break;
            
            $consumir_deste_lote = min($cantidad_por_consumir, $lote['cantidad_restante']);
            $costo_total += $consumir_deste_lote * $lote['precio_compra'];
            
            $nueva_cantidad_restante = $lote['cantidad_restante'] - $consumir_deste_lote;
            $this->update(['cantidad_restante' => $nueva_cantidad_restante], 'id = ?', [$lote['id']]);
            
            $cantidad_por_consumir -= $consumir_deste_lote;
        }
        
        return $costo_total;
    }
    
    public function calcularPrecioPromedio($repuesto_id) {
        $sql = "SELECT SUM(cantidad_restante * precio_compra) as valor_total, SUM(cantidad_restante) as cantidad_total FROM lotes_inventario WHERE repuesto_id = ? AND cantidad_restante > 0";
        $result = $this->fetchOne($sql, [$repuesto_id]);
        
        if (!$result || $result['cantidad_total'] == 0) return 0;
        
        return round($result['valor_total'] / $result['cantidad_total'], 2);
    }
    
    public function obtenerPrecioAnterior($repuesto_id) {
        $sql = "SELECT precio_compra FROM lotes_inventario WHERE repuesto_id = ? ORDER BY fecha_entrada ASC LIMIT 1";
        $result = $this->fetchOne($sql, [$repuesto_id]);
        return $result ? $result['precio_compra'] : 0;
    }
    
    public function obtenerPrecioActual($repuesto_id) {
        $sql = "SELECT precio_compra FROM lotes_inventario WHERE repuesto_id = ? AND cantidad_restante > 0 ORDER BY fecha_entrada DESC LIMIT 1";
        $result = $this->fetchOne($sql, [$repuesto_id]);
        return $result ? $result['precio_compra'] : 0;
    }
}
