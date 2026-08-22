<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Asistencia.php';
require_once __DIR__ . '/../Models/Inspeccion.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/Sucursal.php';

class RRHHController extends Controller {
    private $usuarioModel;
    private $asistenciaModel;
    private $inspeccionModel;
    private $asignacionTecnicoModel;
    private $sucursalModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->asistenciaModel = new Asistencia();
        $this->inspeccionModel = new Inspeccion();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->sucursalModel = new Sucursal();
        $this->verificarSesion();
        $this->verificarRol(['rrhh']);
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
        $total_personal = count($this->usuarioModel->obtenerTodos());
        $fecha = date('Y-m-d');
        $stats = $this->asistenciaModel->obtenerCountPorFecha($fecha);
        
        $this->view('rrhh/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'total_personal' => $total_personal,
            'presentes' => $stats['presentes'] ?? 0,
            'tardanzas' => $stats['tardanzas'] ?? 0,
            'ausentes' => $stats['ausentes'] ?? 0
        ]);
    }
    
    public function asistencia() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $sucursal_id = $_GET['sucursal'] ?? null;
        
        $asistencias = $this->asistenciaModel->obtenerReporte($fecha, $sucursal_id);
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('rrhh/asistencia', [
            'usuario' => $this->obtenerUsuarioActual(),
            'asistencias' => $asistencias,
            'fecha' => $fecha,
            'sucursales' => $sucursales
        ]);
    }
    
    public function inspecciones() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $sucursal_id = $_GET['sucursal'] ?? null;
        
        $inspecciones = $this->inspeccionModel->obtenerReporte($fecha, $sucursal_id);
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('rrhh/inspecciones', [
            'usuario' => $this->obtenerUsuarioActual(),
            'inspecciones' => $inspecciones,
            'fecha' => $fecha,
            'sucursales' => $sucursales
        ]);
    }
    
    public function productividad() {
        $sucursal_id = $_GET['sucursal'] ?? null;
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $tecnicos_data = [];
        $lista_sucursales = $sucursal_id ? [$this->sucursalModel->obtenerPorId($sucursal_id)] : $sucursales;
        
        foreach ($lista_sucursales as $sucursal) {
            $tecnicos = $this->usuarioModel->obtenerTecnicosPorSucursal($sucursal['id']);
            foreach ($tecnicos as $tecnico) {
                $tecnico['sucursal_nombre'] = $sucursal['nombre'];
                $tecnicos_data[] = $tecnico;
            }
        }
        
        $this->view('rrhh/productividad', [
            'usuario' => $this->obtenerUsuarioActual(),
            'tecnicos' => $tecnicos_data,
            'sucursales' => $sucursales
        ]);
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
