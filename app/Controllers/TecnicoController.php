<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/SeguimientoTrabajo.php';

class TecnicoController extends Controller {
    private $equipoModel;
    private $usuarioModel;
    private $asignacionTecnicoModel;
    private $seguimientoModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->usuarioModel = new Usuario();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->seguimientoModel = new SeguimientoTrabajo();
        $this->verificarSesion();
        $this->verificarRol(['tecnico']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    public function dashboard() {
        $tecnico_id = $_SESSION['usuario_id'];
        $mis_trabajos = $this->asignacionTecnicoModel->contarTrabajosActivos($tecnico_id);
        $disponibles = 4 - $mis_trabajos;
        
        $this->view('tecnico/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'mis_trabajos' => $mis_trabajos,
            'disponibles' => $disponibles
        ]);
    }
    
    public function misTrabajos() {
        $tecnico_id = $_SESSION['usuario_id'];
        $trabajos = $this->equipoModel->obtenerTrabajosTecnico($tecnico_id);
        
        $this->view('tecnico/mis_trabajos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'trabajos' => $trabajos
        ]);
    }
    
    public function actualizarTrabajo() {
        $equipo_id = $_POST['equipo_id'];
        $accion = $_POST['accion'];
        $descripcion = $_POST['descripcion'] ?? '';
        
        $this->seguimientoModel->registrar($equipo_id, $_SESSION['usuario_id'], $accion, $descripcion);
        
        if ($accion === 'completado') {
            $this->equipoModel->actualizar($equipo_id, ['estado' => 'completado']);
        }
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    public function confirmarRecibido() {
        $equipo_id = $_POST['equipo_id'];
        
        $this->seguimientoModel->registrar($equipo_id, $_SESSION['usuario_id'], 'recibido', 'El técnico confirma que ha recibido el trabajo');
        $this->equipoModel->actualizar($equipo_id, ['estado' => 'recibido']);
        
        header('HTTP/1.1 200 OK');
        exit;
    }
    
    public function rechazarTrabajo() {
        $equipo_id = $_POST['equipo_id'];
        $motivo = $_POST['motivo'] ?? '';
        
        $this->seguimientoModel->registrar($equipo_id, $_SESSION['usuario_id'], 'rechazado', $motivo);
        $this->equipoModel->actualizar($equipo_id, ['estado' => 'asignado_sucursal']);
        
        $this->asignacionTecnicoModel->eliminarPorEquipo($equipo_id);
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
