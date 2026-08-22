<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Repuesto.php';
require_once __DIR__ . '/../Models/MovimientoInventario.php';
require_once __DIR__ . '/../Models/PedidoRepuesto.php';
require_once __DIR__ . '/../Models/Usuario.php';

class AlmacenController extends Controller {
    private $repuestoModel;
    private $movimientoModel;
    private $pedidoModel;
    private $usuarioModel;
    
    public function __construct() {
        $this->repuestoModel = new Repuesto();
        $this->movimientoModel = new MovimientoInventario();
        $this->pedidoModel = new PedidoRepuesto();
        $this->usuarioModel = new Usuario();
        $this->verificarSesion();
        $this->verificarRol(['almacenista']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    private function verificarRol($roles) {
        if (!in_array($_SESSION['usuario_rol'], $roles)) {
            $this->redirect('');
        }
    }
    
    public function dashboard() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $total_repuestos = count($this->repuestoModel->obtenerPorSucursal($sucursal_id));
        $stock_bajo = $this->repuestoModel->obtenerStockBajo($sucursal_id);
        
        $this->view('almacen/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'total_repuestos' => $total_repuestos,
            'stock_bajo' => $stock_bajo
        ]);
    }
    
    public function inventario() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        
        $this->view('almacen/inventario', [
            'usuario' => $this->obtenerUsuarioActual(),
            'repuestos' => $repuestos
        ]);
    }
    
    public function guardarRepuesto() {
        $data = [
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'stock' => $_POST['stock'] ?? 0,
            'stock_minimo' => $_POST['stock_minimo'] ?? 5,
            'precio_unitario' => $_POST['precio_unitario'] ?? 0,
            'sucursal_id' => $_SESSION['sucursal_id']
        ];
        
        $this->repuestoModel->crear($data);
        $this->redirect('almacen/inventario');
    }
    
    public function movimientos() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        $movimientos = $this->movimientoModel->obtenerPorSucursal($sucursal_id);
        
        $this->view('almacen/movimientos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'repuestos' => $repuestos,
            'movimientos' => $movimientos
        ]);
    }
    
    public function guardarMovimiento() {
        $repuesto_id = $_POST['repuesto_id'];
        $tipo = $_POST['tipo'];
        $cantidad = $_POST['cantidad'];
        
        $repuesto = $this->repuestoModel->obtenerPorId($repuesto_id);
        
        if ($tipo === 'salida' && $repuesto['stock'] < $cantidad) {
            $this->redirect('almacen/movimientos');
            return;
        }
        
        $this->movimientoModel->registrar([
            'repuesto_id' => $repuesto_id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'motivo' => $_POST['motivo'] ?? '',
            'almacenista_id' => $_SESSION['usuario_id'],
            'equipo_id' => $_POST['equipo_id'] ?? null
        ]);
        
        $nuevo_stock = $tipo === 'entrada' ? $repuesto['stock'] + $cantidad : $repuesto['stock'] - $cantidad;
        $this->repuestoModel->actualizar($repuesto_id, ['stock' => $nuevo_stock]);
        
        $this->redirect('almacen/movimientos');
    }
    
    public function pedidos() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        $pedidos = $this->pedidoModel->obtenerPorSucursal($sucursal_id);
        
        $this->view('almacen/pedidos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'repuestos' => $repuestos,
            'pedidos' => $pedidos
        ]);
    }
    
    public function guardarPedido() {
        $this->pedidoModel->crear([
            'sucursal_id' => $_SESSION['sucursal_id'],
            'repuesto_id' => $_POST['repuesto_id'],
            'cantidad' => $_POST['cantidad'],
            'solicitado_por' => $_SESSION['usuario_id']
        ]);
        
        $this->redirect('almacen/pedidos');
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
