<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/Sucursal.php';
require_once __DIR__ . '/../Models/SeguimientoTrabajo.php';
require_once __DIR__ . '/../Models/CalificacionTecnico.php';

class JefeTecnicoController extends Controller {
    private $equipoModel;
    private $usuarioModel;
    private $asignacionTecnicoModel;
    private $seguimientoModel;
    private $calificacionModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->usuarioModel = new Usuario();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->seguimientoModel = new SeguimientoTrabajo();
        $this->calificacionModel = new CalificacionTecnico();
        $this->verificarSesion();
        $this->verificarRol(['jefe_tecnico']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
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
    
    public function obtenerDetallesEquipo() {
        $equipo_id = $_GET['equipo_id'] ?? 0;
        
        $seguimiento = $this->seguimientoModel->obtenerPorEquipo($equipo_id);
        
        echo json_encode([
            'seguimiento' => $seguimiento
        ]);
        exit;
    }
    
    public function aprobarTrabajo() {
        $equipo_id = $_POST['equipo_id'];
        
        $this->equipoModel->actualizar($equipo_id, ['estado' => 'entregado']);
        
        header('HTTP/1.1 200 OK');
        exit;
    }
    
    public function calificar() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');
        
        $tecnicos = $this->calificacionModel->obtenerTecnicosPorSucursal($sucursal_id);
        $ranking = $this->calificacionModel->obtenerRankingPorMes($sucursal_id, $mes, $anio);
        
        $this->view('jefe_tecnico/calificar', [
            'usuario' => $this->obtenerUsuarioActual(),
            'tecnicos' => $tecnicos,
            'ranking' => $ranking,
            'mes' => $mes,
            'anio' => $anio
        ]);
    }
    
    public function guardarCalificacion() {
        $tecnico_id = $_POST['tecnico_id'];
        $puntuacion_trabajo = floatval($_POST['puntuacion_trabajo']);
        $puntuacion_asistencia = floatval($_POST['puntuacion_asistencia'] ?? 0);
        $observaciones = $_POST['observaciones'] ?? '';
        
        $mes = intval($_POST['mes']);
        $anio = intval($_POST['anio']);
        
        $puntuacion_total = ($puntuacion_trabajo + $puntuacion_asistencia) / 2;
        
        $this->calificacionModel->guardar([
            'tecnico_id' => $tecnico_id,
            'jefe_tecnico_id' => $_SESSION['usuario_id'],
            'mes' => $mes,
            'anio' => $anio,
            'puntuacion_trabajo' => $puntuacion_trabajo,
            'puntuacion_asistencia' => $puntuacion_asistencia,
            'puntuacion_total' => $puntuacion_total,
            'observaciones' => $observaciones
        ]);
        
        $_SESSION['mensaje_exito'] = 'Calificación guardada correctamente';
        $this->redirect('jefe-tecnico/calificar?mes=' . $mes . '&anio=' . $anio);
    }
    
    public function obtenerAsistencias() {
        $tecnico_id = $_GET['tecnico_id'] ?? null;
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');
        
        if (!$tecnico_id) {
            echo json_encode(['error' => 'Técnico no especificado']);
            exit;
        }
        
        $asistencias = $this->calificacionModel->obtenerAsistenciasAprobadasPorTecnicoYMes($tecnico_id, $mes, $anio);
        
        echo json_encode($asistencias);
        exit;
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
