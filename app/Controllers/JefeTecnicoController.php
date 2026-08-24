<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/Sucursal.php';

class JefeTecnicoController extends Controller {
    private $equipoModel;
    private $usuarioModel;
    private $asignacionTecnicoModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->usuarioModel = new Usuario();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->verificarSesion();
        $this->verificarRol(['jefe_tecnico']);
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
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $sucursal_id = $_SESSION['sucursal_id'];
        $tecnicos = $this->usuarioModel->obtenerTecnicosPorSucursal($sucursal_id);
        $pendientes = $this->equipoModel->obtenerAsignadosSinTecnico($sucursal_id);
        
        $this->view('jefe_tecnico/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'tecnicos' => $tecnicos,
            'pendientes' => count($pendientes)
        ]);
    }
    
    public function asignarTecnicos() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $equipos = $this->equipoModel->obtenerAsignadosSinTecnico($sucursal_id);
        $tecnicos = $this->usuarioModel->obtenerTecnicosPorSucursal($sucursal_id);
        
        $this->view('jefe_tecnico/asignar_tecnicos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipos' => $equipos,
            'tecnicos' => $tecnicos
        ]);
    }
    
    public function guardarAsignacion() {
        $equipo_id = $_POST['equipo_id'];
        $tecnico_id = $_POST['tecnico_id'];
        
        $trabajos_actuales = $this->asignacionTecnicoModel->contarTrabajosActivos($tecnico_id);
        
        if ($trabajos_actuales >= 4) {
            $this->redirect('jefe-tecnico/asignar-tecnicos');
            return;
        }
        
        $this->asignacionTecnicoModel->asignar($equipo_id, $tecnico_id, $_SESSION['usuario_id']);
        $this->equipoModel->actualizar($equipo_id, ['estado' => 'en_reparacion']);
        
        $this->redirect('jefe-tecnico/asignar-tecnicos');
    }
    
    public function seguimiento() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $asignaciones = $this->asignacionTecnicoModel->obtenerPorSucursal($sucursal_id);
        
        $this->view('jefe_tecnico/seguimiento', [
            'usuario' => $this->obtenerUsuarioActual(),
            'asignaciones' => $asignaciones
        ]);
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
