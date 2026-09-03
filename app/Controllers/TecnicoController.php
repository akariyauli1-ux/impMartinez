<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/SeguimientoTrabajo.php';
require_once __DIR__ . '/../Models/SolicitudComponente.php';
require_once __DIR__ . '/../Models/Repuesto.php';

class TecnicoController extends Controller {
    private $equipoModel;
    private $usuarioModel;
    private $asignacionTecnicoModel;
    private $seguimientoModel;
    private $solicitudModel;
    private $repuestoModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->usuarioModel = new Usuario();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->seguimientoModel = new SeguimientoTrabajo();
        $this->solicitudModel = new SolicitudComponente();
        $this->repuestoModel = new Repuesto();
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
        
        // Obtener filtros
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'dia' => $_GET['dia'] ?? '',
            'mes' => $_GET['mes'] ?? '',
            'anio' => $_GET['anio'] ?? ''
        ];
        
        // Verificar si hay filtros activos
        $hay_filtros = !empty($filtros['estado']) || !empty($filtros['dia']) || !empty($filtros['mes']) || !empty($filtros['anio']);
        
        if ($hay_filtros) {
            $trabajos = $this->equipoModel->obtenerTrabajosTecnicoConFiltros($tecnico_id, $filtros);
        } else {
            $trabajos = $this->equipoModel->obtenerTrabajosTecnico($tecnico_id);
        }
        
        $solicitudes_enviadas = $this->solicitudModel->obtenerEnviadasTecnico($tecnico_id);
        $solicitudes_agotadas = $this->solicitudModel->obtenerAgotadasTecnico($tecnico_id);
        
        $this->solicitudModel->marcarNotificacionesLeidas($tecnico_id);
        
        $this->view('tecnico/mis_trabajos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'trabajos' => $trabajos,
            'solicitudes_enviadas' => $solicitudes_enviadas,
            'solicitudes_agotadas' => $solicitudes_agotadas,
            'filtros' => $filtros
        ]);
    }
    
    public function solicitarComponente() {
        $equipo_id = $_POST['equipo_id'];
        $repuesto_id = $_POST['repuesto_id'];
        $cantidad = $_POST['cantidad'];
        $motivo = $_POST['motivo'] ?? '';
        
        $repuesto = $this->repuestoModel->obtenerPorId($repuesto_id);
        if (!$repuesto) {
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        $disponibles = $repuesto['unidades_disponibles'] ?? 0;
        if ($disponibles <= 0) {
            $_SESSION['error_solicitud'] = 'YA NO HAY DISPONIBLE EN ALMACEN para "' . $repuesto['nombre'] . '"';
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        if ($cantidad > $disponibles) {
            $_SESSION['error_solicitud'] = 'No puedes solicitar más de ' . $disponibles . ' unidades. Stock disponible: ' . $disponibles;
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        $precio_unitario = $repuesto['precio_unitario'] ?? 0;
        $total = $precio_unitario * $cantidad;
        
        $this->solicitudModel->crear([
            'equipo_id' => $equipo_id,
            'tecnico_id' => $_SESSION['usuario_id'],
            'repuesto_id' => $repuesto_id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total,
            'motivo' => $motivo,
            'estado' => 'solicitado'
        ]);
        
        $this->repuestoModel->descontarStockReservado($repuesto_id, $cantidad);
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    public function obtenerRepuestos() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        
        header('Content-Type: application/json');
        echo json_encode($repuestos);
        exit;
    }
    
    public function obtenerCostoEquipo() {
        $equipo_id = $_GET['equipo_id'] ?? null;
        
        if (!$equipo_id) {
            header('Content-Type: application/json');
            echo json_encode(['total' => 0, 'solicitudes' => []]);
            exit;
        }
        
        $total = $this->solicitudModel->obtenerCostoTotalEquipo($equipo_id);
        $solicitudes = $this->solicitudModel->obtenerPorEquipo($equipo_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'total' => $total,
            'solicitudes' => $solicitudes
        ]);
        exit;
    }
    
    public function actualizarTrabajo() {
        $equipo_id = $_POST['equipo_id'];
        $accion = $_POST['accion'];
        $descripcion = $_POST['descripcion'] ?? '';
        
        $this->seguimientoModel->registrar($equipo_id, $_SESSION['usuario_id'], $accion, $descripcion);
        
        if ($accion === 'completado') {
            $this->equipoModel->actualizar($equipo_id, ['estado' => 'completado']);
        } elseif ($accion === 'pausado') {
            $this->equipoModel->actualizar($equipo_id, ['estado' => 'pausado']);
        } elseif ($accion === 'inicio_reparacion') {
            $this->equipoModel->actualizar($equipo_id, ['estado' => 'en_reparacion']);
        } elseif ($accion === 'reanudar') {
            $this->equipoModel->actualizar($equipo_id, ['estado' => 'en_reparacion']);
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
