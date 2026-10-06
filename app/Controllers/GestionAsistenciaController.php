<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Asistencia.php';
require_once __DIR__ . '/../Models/Usuario.php';

class GestionAsistenciaController extends Controller {
    private $asistenciaModel;
    private $usuarioModel;
    
    public function __construct() {
        $this->asistenciaModel = new Asistencia();
        $this->usuarioModel = new Usuario();
        $this->verificarSesion();
        $this->verificarRol(['admin_sucursal']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    public function index() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        
        $pendientes = $this->asistenciaModel->obtenerPendientesPorSucursal($sucursal_id, $fecha);
        $historial = $this->asistenciaModel->obtenerHistorialPorSucursal($sucursal_id, $fecha);
        $resumen = $this->asistenciaModel->obtenerResumenDelDia($sucursal_id, $fecha);
        
        $this->view('gestion_asistencia/index', [
            'usuario' => $this->obtenerUsuarioActual(),
            'pendientes' => $pendientes,
            'historial' => $historial,
            'resumen' => $resumen,
            'fecha' => $fecha
        ]);
    }
    
    public function aprobar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('gestion-asistencia');
            return;
        }
        
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $_SESSION['error_asistencia'] = 'ID de asistencia no válido';
            $this->redirect('gestion-asistencia');
            return;
        }
        
        $this->asistenciaModel->aprobar($id, $_SESSION['usuario_id']);
        
        $_SESSION['mensaje_exito'] = 'Asistencia aprobada correctamente';
        $this->redirect('gestion-asistencia');
    }
    
    public function rechazar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('gestion-asistencia');
            return;
        }
        
        $id = $_POST['id'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (!$id) {
            $_SESSION['error_asistencia'] = 'ID de asistencia no válido';
            $this->redirect('gestion-asistencia');
            return;
        }
        
        $this->asistenciaModel->rechazar($id, $_SESSION['usuario_id'], $observaciones);
        
        $_SESSION['mensaje_exito'] = 'Asistencia rechazada';
        $this->redirect('gestion-asistencia');
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
