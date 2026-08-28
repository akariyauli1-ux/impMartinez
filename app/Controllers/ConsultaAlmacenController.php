<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Repuesto.php';

class ConsultaAlmacenController extends Controller {
    private $repuestoModel;
    
    public function __construct() {
        $this->repuestoModel = new Repuesto();
        $this->verificarSesion();
        $this->verificarRol(['recepcionista', 'almacenista', 'tecnico', 'admin_sucursal', 'gerente']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    public function obtenerRepuestos() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        
        header('Content-Type: application/json');
        echo json_encode($repuestos);
        exit;
    }
}
