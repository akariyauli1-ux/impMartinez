<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/PedidoRepuesto.php';
require_once __DIR__ . '/../Models/Repuesto.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Sucursal.php';

class PedidoController extends Controller {
    private $pedidoModel;
    private $repuestoModel;
    private $usuarioModel;
    private $sucursalModel;
    
    public function __construct() {
        $this->pedidoModel = new PedidoRepuesto();
        $this->repuestoModel = new Repuesto();
        $this->usuarioModel = new Usuario();
        $this->sucursalModel = new Sucursal();
        $this->verificarSesion();
        $this->verificarRol(['tecnico', 'admin_sucursal', 'jefe_tecnico', 'recepcionista', 'gerente', 'almacenista']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    public function index() {
        $rol = $_SESSION['rol_activo'] ?? $_SESSION['usuario_rol'];
        
        if ($rol === 'almacenista') {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $mis_pedidos = $this->pedidoModel->obtenerPorSolicitante($usuario_id);
        $pendientes_confirmacion = $this->pedidoModel->obtenerRespondidosPorSolicitante($usuario_id);
        
        $this->view('pedidos/index', [
            'usuario' => $this->obtenerUsuarioActual(),
            'mis_pedidos' => $mis_pedidos,
            'pendientes_confirmacion' => $pendientes_confirmacion
        ]);
    }
    
    public function nuevo() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal_id);
        
        if (empty($repuestos)) {
            $repuestos = $this->repuestoModel->obtenerTodos();
        }
        
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('pedidos/nuevo', [
            'usuario' => $this->obtenerUsuarioActual(),
            'repuestos' => $repuestos,
            'sucursales' => $sucursales
        ]);
    }
    
    public function guardar() {
        $repuesto_id = $_POST['repuesto_id'] ?? null;
        $cantidad = $_POST['cantidad'] ?? 1;
        $sucursal_id = $_POST['sucursal_id'] ?? $_SESSION['sucursal_id'];
        $descripcion = $_POST['descripcion'] ?? '';
        
        if (!$repuesto_id || !$cantidad) {
            $this->redirect('pedidos/nuevo');
            return;
        }
        
        $repuesto = $this->repuestoModel->obtenerPorId($repuesto_id);
        
        if (!$repuesto) {
            $this->redirect('pedidos/nuevo');
            return;
        }
        
        if (($repuesto['stock'] ?? 0) < $cantidad) {
            $_SESSION['error_pedido'] = 'Stock insuficiente. Disponible: ' . $repuesto['stock'];
            $this->redirect('pedidos/nuevo');
            return;
        }
        
        $precio_unitario = $repuesto['precio_unitario'] ?? 0;
        $total = $precio_unitario * $cantidad;
        
        $this->pedidoModel->crear([
            'sucursal_id' => $sucursal_id,
            'repuesto_id' => $repuesto_id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total,
            'solicitado_por' => $_SESSION['usuario_id'],
            'tecnico_id' => $_SESSION['usuario_id'],
            'estado' => 'solicitado'
        ]);
        
        $this->repuestoModel->descontarStockReservado($repuesto_id, $cantidad);
        
        $this->redirect('pedidos');
    }
    
    public function almacen() {
        $this->verificarRol(['almacenista']);
        
        $pendientes = $this->pedidoModel->obtenerPendientesAlmacen();
        $todos = $this->pedidoModel->obtenerTodos();
        $total_pendientes = count($pendientes);
        
        require_once __DIR__ . '/../Models/SolicitudComponente.php';
        $solicitudModel = new \SolicitudComponente();
        $solicitudes = $solicitudModel->obtenerTodas();
        
        $this->view('pedidos/almacen', [
            'usuario' => $this->obtenerUsuarioActual(),
            'pendientes' => $pendientes,
            'todos_pedidos' => $todos,
            'total_pendientes' => $total_pendientes,
            'solicitudes' => $solicitudes
        ]);
    }
    
    public function responder() {
        $this->verificarRol(['almacenista']);
        
        $pedido_id = $_POST['pedido_id'] ?? null;
        $tipo_respuesta = $_POST['tipo_respuesta'] ?? null;
        $respuesta_texto = $_POST['respuesta_texto'] ?? '';
        
        if (!$pedido_id || !$tipo_respuesta) {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        $pedido = $this->pedidoModel->obtenerPorId($pedido_id);
        
        if (!$pedido) {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        $this->pedidoModel->responder($pedido_id, $tipo_respuesta, $respuesta_texto, $_SESSION['usuario_id']);
        
        if ($tipo_respuesta === 'enviando') {
            $this->repuestoModel->confirmarSalidaInventario($pedido['repuesto_id'], $pedido['cantidad'], $pedido['solicitado_por']);
        } else {
            $this->repuestoModel->devolverStockReservado($pedido['repuesto_id'], $pedido['cantidad']);
        }
        
        $this->redirect('pedidos/almacen');
    }
    
    public function confirmarRecibido() {
        $pedido_id = $_POST['pedido_id'] ?? null;
        
        if (!$pedido_id) {
            $this->redirect('pedidos');
            return;
        }
        
        $pedido = $this->pedidoModel->obtenerPorId($pedido_id);
        
        if (!$pedido || $pedido['solicitado_por'] != $_SESSION['usuario_id']) {
            $this->redirect('pedidos');
            return;
        }
        
        $this->pedidoModel->confirmarRecibido($pedido_id, $_SESSION['usuario_id']);
        
        $this->redirect('pedidos');
    }
    
    public function confirmarLeido() {
        $pedido_id = $_POST['pedido_id'] ?? null;
        
        if (!$pedido_id) {
            $this->redirect('pedidos');
            return;
        }
        
        $pedido = $this->pedidoModel->obtenerPorId($pedido_id);
        
        if (!$pedido || $pedido['solicitado_por'] != $_SESSION['usuario_id']) {
            $this->redirect('pedidos');
            return;
        }
        
        $this->pedidoModel->confirmarLeido($pedido_id, $_SESSION['usuario_id']);
        
        $this->redirect('pedidos');
    }
    
    public function entregarSolicitud() {
        $this->verificarRol(['almacenista']);
        
        $solicitud_id = $_POST['solicitud_id'] ?? null;
        
        if (!$solicitud_id) {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        require_once __DIR__ . '/../Models/SolicitudComponente.php';
        $solicitudModel = new \SolicitudComponente();
        
        $solicitud = $solicitudModel->obtenerPorIdConDetalles($solicitud_id);
        
        if (!$solicitud) {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        $solicitudModel->actualizarEstado($solicitud_id, 'enviado');
        
        $this->repuestoModel->actualizarStock($solicitud['repuesto_id'], $solicitud['cantidad'], 'resta');
        $this->repuestoModel->incrementarSolicitudes($solicitud['repuesto_id'], $solicitud['cantidad']);
        
        $this->redirect('pedidos/almacen');
    }
    
    public function confirmarRecibidoSolicitud() {
        $solicitud_id = $_POST['solicitud_id'] ?? null;
        
        if (!$solicitud_id) {
            $this->redirect('tecnico/mis-trabajos');
            return;
        }
        
        require_once __DIR__ . '/../Models/SolicitudComponente.php';
        $solicitudModel = new \SolicitudComponente();
        
        $tecnico_id = $_SESSION['usuario_id'];
        $solicitudModel->confirmarRecibido($solicitud_id, $tecnico_id);
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    public function historial() {
        $rol = $_SESSION['rol_activo'] ?? $_SESSION['usuario_rol'];
        
        if ($rol === 'almacenista') {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        
        $filtros = [
            'estado' => $_GET['estado'] ?? null,
            'fecha_desde' => $_GET['fecha_desde'] ?? null,
            'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
            'busqueda' => $_GET['busqueda'] ?? null,
        ];
        
        $mis_pedidos = $this->pedidoModel->obtenerHistorialSolicitante($usuario_id, $filtros);
        $contadores = $this->pedidoModel->contarPorEstadoSolicitante($usuario_id);
        
        $this->view('pedidos/historial', [
            'usuario' => $this->obtenerUsuarioActual(),
            'mis_pedidos' => $mis_pedidos,
            'contadores' => $contadores,
            'filtros' => $filtros
        ]);
    }
    
    public function notificacion() {
        $usuario_id = $_SESSION['usuario_id'];
        $rol = $_SESSION['rol_activo'] ?? $_SESSION['usuario_rol'];
        $sucursal_id = $_SESSION['sucursal_id'];
        
        $notificaciones = [];
        
        // Notificaciones de pedidos
        if ($rol === 'almacenista') {
            $cantidad_pedidos = $this->pedidoModel->contarPendientesAlmacen();
            if ($cantidad_pedidos > 0) {
                $notificaciones[] = [
                    'tipo' => 'pedidos',
                    'cantidad' => $cantidad_pedidos,
                    'mensaje' => "$cantidad_pedidos pedido(s) pendiente(s) de respuesta",
                    'url' => APP_URL . '/public/pedidos/almacen',
                    'icono' => '📦'
                ];
            }
        } else {
            $cantidad_pedidos = $this->pedidoModel->contarPendientesConfirmacion($usuario_id);
            if ($cantidad_pedidos > 0) {
                $notificaciones[] = [
                    'tipo' => 'pedidos',
                    'cantidad' => $cantidad_pedidos,
                    'mensaje' => "$cantidad_pedidos respuesta(s) de almacen pendiente(s) de confirmacion",
                    'url' => APP_URL . '/public/pedidos',
                    'icono' => '📦'
                ];
            }
        }
        
        // Notificaciones de trabajos segun el rol
        $equipoModel = new \Equipo();
        
        if ($rol === 'tecnico') {
            $trabajos_nuevos = $equipoModel->contarTrabajosNuevosParaTecnico($usuario_id);
            if ($trabajos_nuevos > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_nuevos,
                    'mensaje' => "$trabajos_nuevos trabajo(s) nuevo(s) asignado(s)",
                    'url' => APP_URL . '/public/tecnico/mis-trabajos',
                    'icono' => '🔧'
                ];
            }
        } elseif ($rol === 'jefe_tecnico') {
            $trabajos_pendientes = $equipoModel->contarTrabajosPendientesAsignarJefe($sucursal_id);
            if ($trabajos_pendientes > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_pendientes,
                    'mensaje' => "$trabajos_pendientes trabajo(s) pendiente(s) de asignar a tecnico",
                    'url' => APP_URL . '/public/jefe-tecnico/asignar-tecnicos',
                    'icono' => '👷'
                ];
            }
        } elseif ($rol === 'recepcionista') {
            $trabajos_completados = $equipoModel->contarTrabajosCompletadosParaRecepcion($sucursal_id);
            if ($trabajos_completados > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_completados,
                    'mensaje' => "$trabajos_completados trabajo(s) completado(s) listo(s) para entrega",
                    'url' => APP_URL . '/public/recepcion/equipos-listos',
                    'icono' => '✅'
                ];
            }
        } elseif ($rol === 'admin_sucursal') {
            $trabajos_nuevos = $equipoModel->contarTrabajosNuevosParaAdmin($sucursal_id);
            if ($trabajos_nuevos > 0) {
                $notificaciones[] = [
                    'tipo' => 'trabajos',
                    'cantidad' => $trabajos_nuevos,
                    'mensaje' => "$trabajos_nuevos equipo(s) nuevo(s) pendiente(s) de asignar sucursal",
                    'url' => APP_URL . '/public/admin-sucursal/pendientes',
                    'icono' => '📥'
                ];
            }
        }
        
        $this->json(['notificaciones' => $notificaciones]);
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
