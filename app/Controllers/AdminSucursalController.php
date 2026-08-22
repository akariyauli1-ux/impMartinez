<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Sucursal.php';
require_once __DIR__ . '/../Models/AsignacionSucursal.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/Asistencia.php';
require_once __DIR__ . '/../Models/Inspeccion.php';

class AdminSucursalController extends Controller {
    private $equipoModel;
    private $usuarioModel;
    private $sucursalModel;
    private $asignacionSucursalModel;
    private $asignacionTecnicoModel;
    private $asistenciaModel;
    private $inspeccionModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->usuarioModel = new Usuario();
        $this->sucursalModel = new Sucursal();
        $this->asignacionSucursalModel = new AsignacionSucursal();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->asistenciaModel = new Asistencia();
        $this->inspeccionModel = new Inspeccion();
        $this->verificarSesion();
        $this->verificarRol(['admin_sucursal']);
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
        $pendientes = $this->equipoModel->obtenerCountPorEstado('asignado_sucursal', $sucursal_id);
        $en_reparacion = $this->equipoModel->obtenerCountPorEstado('en_reparacion', $sucursal_id);
        $completados = $this->equipoModel->obtenerCountPorEstado('completado', $sucursal_id);
        
        $this->view('admin_sucursal/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'pendientes' => $pendientes,
            'en_reparacion' => $en_reparacion,
            'completados' => $completados
        ]);
    }
    
    public function pendientes() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $pendientes = $this->equipoModel->obtenerAsignadosSinTecnico($sucursal_id);
        
        $this->view('admin_sucursal/pendientes', [
            'usuario' => $this->obtenerUsuarioActual(),
            'pendientes' => $pendientes
        ]);
    }
    
    public function asignar() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $equipos = $this->equipoModel->obtenerPendientesPorSucursal($sucursal_id);
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('admin_sucursal/asignar', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipos' => $equipos,
            'sucursales' => $sucursales
        ]);
    }
    
    public function guardarAsignacion() {
        $equipo_id = $_POST['equipo_id'];
        $sucursal_destino = $_POST['sucursal_destino'];
        $sucursal_origen = $_SESSION['sucursal_id'];
        
        $this->equipoModel->actualizar($equipo_id, [
            'sucursal_actual_id' => $sucursal_destino,
            'estado' => 'asignado_sucursal'
        ]);
        
        $this->asignacionSucursalModel->asignar(
            $equipo_id,
            $sucursal_origen,
            $sucursal_destino,
            $_SESSION['usuario_id'],
            $_POST['motivo'] ?? ''
        );
        
        $this->redirect('admin-sucursal/asignar');
    }
    
    public function asistencia() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $sucursal_id = $_SESSION['sucursal_id'];
        $personal = $this->asistenciaModel->obtenerPorFechaYSucursal($fecha, $sucursal_id);
        
        $this->view('admin_sucursal/asistencia', [
            'usuario' => $this->obtenerUsuarioActual(),
            'personal' => $personal,
            'fecha' => $fecha
        ]);
    }
    
    public function guardarAsistencia() {
        $fecha = $_POST['fecha'];
        
        if (isset($_POST['empleados'])) {
            foreach ($_POST['empleados'] as $emp_id => $datos) {
                $this->asistenciaModel->registrar([
                    'usuario_id' => $emp_id,
                    'fecha' => $fecha,
                    'hora_entrada' => $datos['hora_entrada'] ?? null,
                    'hora_salida' => $datos['hora_salida'] ?? null,
                    'estado' => $datos['estado'] ?? 'ausente',
                    'observaciones' => $datos['observaciones'] ?? '',
                    'registrado_por' => $_SESSION['usuario_id']
                ]);
            }
        }
        
        $this->redirect('admin-sucursal/asistencia?fecha=' . $fecha);
    }
    
    public function inspecciones() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $sucursal_id = $_SESSION['sucursal_id'];
        $personal = $this->inspeccionModel->obtenerPorFechaYSucursal($fecha, $sucursal_id);
        
        $this->view('admin_sucursal/inspecciones', [
            'usuario' => $this->obtenerUsuarioActual(),
            'personal' => $personal,
            'fecha' => $fecha
        ]);
    }
    
    public function guardarInspecciones() {
        $fecha = $_POST['fecha'];
        
        if (isset($_POST['inspecciones'])) {
            foreach ($_POST['inspecciones'] as $emp_id => $datos) {
                $this->inspeccionModel->registrar([
                    'usuario_id' => $emp_id,
                    'fecha' => $fecha,
                    'limpieza' => isset($datos['limpieza_check']) ? 'aprobado' : 'rechazado',
                    'uniforme' => isset($datos['uniforme_check']) ? 'completo' : 'incompleto',
                    'hora_limpieza' => $datos['hora_limpieza'] ?? null,
                    'hora_uniforme' => $datos['hora_uniforme'] ?? null,
                    'obs_limpieza' => $datos['obs_limpieza'] ?? '',
                    'obs_uniforme' => $datos['obs_uniforme'] ?? '',
                    'observaciones' => $datos['observaciones'] ?? '',
                    'registrado_por' => $_SESSION['usuario_id']
                ]);
            }
        }
        
        $this->redirect('admin-sucursal/inspecciones?fecha=' . $fecha);
    }
    
    public function reportes() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $reportes = $this->equipoModel->obtenerEstadosPorSucursal($sucursal_id);
        
        $this->view('admin_sucursal/reportes', [
            'usuario' => $this->obtenerUsuarioActual(),
            'reportes' => $reportes
        ]);
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
