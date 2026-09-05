<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/SeguimientoTrabajo.php';
require_once __DIR__ . '/../Models/SolicitudComponente.php';
require_once __DIR__ . '/../Models/SolicitudRepuestoNuevo.php';
require_once __DIR__ . '/../Models/Repuesto.php';

class TecnicoController extends Controller {
    private $equipoModel;
    private $usuarioModel;
    private $asignacionTecnicoModel;
    private $seguimientoModel;
    private $solicitudModel;
    private $solicitudRepuestoNuevoModel;
    private $repuestoModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->usuarioModel = new Usuario();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->seguimientoModel = new SeguimientoTrabajo();
        $this->solicitudModel = new SolicitudComponente();
        $this->solicitudRepuestoNuevoModel = new SolicitudRepuestoNuevo();
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
        $solicitudes_repuestos_nuevos = $this->solicitudRepuestoNuevoModel->obtenerPorTecnico($tecnico_id);
        $compras_externas_pendientes = $this->solicitudRepuestoNuevoModel->obtenerComprasExternasPendientesTecnico($tecnico_id);
        $componentes_pendientes = $this->solicitudModel->contarPendientesTecnico($tecnico_id);
        
        // Obtener componentes pendientes por equipo
        $componentes_por_equipo = [];
        foreach ($trabajos as $trabajo) {
            $componentes_por_equipo[$trabajo['id']] = $this->solicitudModel->contarPendientesPorEquipo($trabajo['id']);
        }
        
        $this->solicitudModel->marcarNotificacionesLeidas($tecnico_id);
        
        $this->view('tecnico/mis_trabajos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'trabajos' => $trabajos,
            'solicitudes_enviadas' => $solicitudes_enviadas,
            'solicitudes_agotadas' => $solicitudes_agotadas,
            'solicitudes_repuestos_nuevos' => $solicitudes_repuestos_nuevos,
            'compras_externas_pendientes' => $compras_externas_pendientes,
            'componentes_pendientes' => $componentes_pendientes,
            'componentes_por_equipo' => $componentes_por_equipo,
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
    
    public function solicitarCompraExterna() {
        $solicitud_id = $_POST['solicitud_id'];
        $proveedor = $_POST['proveedor'] ?? '';
        $precio_unitario = $_POST['precio_unitario'] ?? 0;
        
        if (!$solicitud_id) {
            $_SESSION['error_solicitud'] = 'Solicitud no encontrada';
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        $solicitud = $this->solicitudModel->obtenerPorIdConDetalles($solicitud_id);
        if (!$solicitud) {
            $_SESSION['error_solicitud'] = 'Solicitud no encontrada';
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        $compra_id = $this->solicitudModel->crearCompraExterna(
            $solicitud_id,
            $solicitud['equipo_id'],
            $solicitud['repuesto_id'],
            $_SESSION['usuario_id'],
            $solicitud['cantidad'],
            $precio_unitario,
            $proveedor
        );
        
        if ($compra_id) {
            $this->solicitudModel->actualizarCompraExternaId($solicitud_id, $compra_id);
            $_SESSION['mensaje_exito'] = 'Solicitud de compra externa registrada. Almacén se encargará de buscar el proveedor y confirmar el precio.';
        } else {
            $_SESSION['error_solicitud'] = 'Error al registrar la solicitud de compra externa';
        }
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    public function solicitarRepuestoNuevo() {
        $equipo_id = $_POST['equipo_id'];
        $nombre_repuesto = trim($_POST['nombre_repuesto'] ?? '');
        $marca = trim($_POST['marca'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $cantidad = intval($_POST['cantidad'] ?? 1);
        $motivo = trim($_POST['motivo'] ?? '');
        
        if (!$equipo_id || !$nombre_repuesto || $cantidad < 1) {
            $_SESSION['error_solicitud'] = 'Por favor completa los campos obligatorios: nombre del repuesto y cantidad';
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        $this->solicitudRepuestoNuevoModel->crear([
            'equipo_id' => $equipo_id,
            'tecnico_id' => $_SESSION['usuario_id'],
            'nombre_repuesto' => $nombre_repuesto,
            'marca' => $marca,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'estado' => 'pendiente'
        ]);
        
        $_SESSION['mensaje_exito'] = 'Solicitud de repuesto nuevo registrada. Almacén buscará el componente y lo agregará al costo de reparación.';
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    public function confirmarRecibidoRepuestoNuevo() {
        $compra_id = $_POST['compra_id'] ?? null;
        
        if (!$compra_id) {
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        $tecnico_id = $_SESSION['usuario_id'];
        
        require_once __DIR__ . '/../Models/SolicitudComponente.php';
        $solicitudComponenteModel = new \SolicitudComponente();
        $compra = $solicitudComponenteModel->obtenerCompraExternaPorId($compra_id);
        
        if (!$compra || $compra['tipo_origen'] !== 'repuesto_nuevo') {
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        $this->solicitudRepuestoNuevoModel->confirmarRecibidoTecnico($compra_id, $tecnico_id);
        
        $costo_total = $compra['precio_unitario'] * $compra['cantidad'];
        $this->solicitudRepuestoNuevoModel->agregarAlCostoReparacion($compra['equipo_id'], $costo_total);
        
        $_SESSION['mensaje_exito'] = 'Repuesto confirmado como recibido. Se agregaron S/ ' . number_format($costo_total, 2) . ' al costo de reparación.';
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
