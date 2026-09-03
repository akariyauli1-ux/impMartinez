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
        
        $filtros = [
            'dia' => $_GET['dia'] ?? '',
            'mes' => $_GET['mes'] ?? '',
            'anio' => $_GET['anio'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];
        
        $hay_filtros = !empty($filtros['dia']) || !empty($filtros['mes']) || !empty($filtros['anio']) || !empty($filtros['estado']);
        
        if ($hay_filtros) {
            $solicitudes = $solicitudModel->obtenerConFiltros($filtros);
        } else {
            $solicitudes = $solicitudModel->obtenerConFiltros([], 10);
        }
        
        $compras_externas = $solicitudModel->obtenerComprasExternas();
        
        $this->view('pedidos/almacen', [
            'usuario' => $this->obtenerUsuarioActual(),
            'pendientes' => $pendientes,
            'todos_pedidos' => $todos,
            'total_pendientes' => $total_pendientes,
            'solicitudes' => $solicitudes,
            'compras_externas' => $compras_externas,
            'filtros' => $filtros
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
        
        $repuesto = $this->repuestoModel->obtenerPorId($solicitud['repuesto_id']);
        $disponibles = $repuesto['unidades_disponibles'] ?? 0;
        
        if ($disponibles < $solicitud['cantidad']) {
            $_SESSION['error_pedido'] = 'YA NO HAY DISPONIBLE EN ALMACEN para "' . $repuesto['nombre'] . '". Stock actual: ' . $disponibles;
            $this->redirect('pedidos/almacen');
            return;
        }
        
        $solicitudModel->actualizarEstado($solicitud_id, 'enviado');
        
        $this->repuestoModel->confirmarSalidaInventario($solicitud['repuesto_id'], $solicitud['cantidad'], $_SESSION['usuario_id']);
        
        $this->redirect('pedidos/almacen');
    }
    
    public function marcarAgotado() {
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
        
        $solicitudModel->actualizarEstado($solicitud_id, 'agotado');
        
        $this->repuestoModel->devolverStockReservado($solicitud['repuesto_id'], $solicitud['cantidad']);
        
        $solicitudModel->actualizarCostoEquipo($solicitud['equipo_id']);
        
        require_once __DIR__ . '/../Models/Equipo.php';
        $equipoModel = new \Equipo();
        $equipo = $equipoModel->obtenerPorId($solicitud['equipo_id']);
        
        $observacion_actual = $equipo['observaciones'] ?? '';
        $nueva_observacion = $observacion_actual . "\n[" . date('d/m/Y H:i') . "] COMPONENTE AGOTADO: " . $solicitud['repuesto_nombre'] . " (Cantidad solicitada: " . $solicitud['cantidad'] . ") - Sin stock disponible en almacen";
        
        $equipoModel->actualizar($solicitud['equipo_id'], ['observaciones' => trim($nueva_observacion)]);
        
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
        
        $solicitud = $solicitudModel->obtenerPorIdConDetalles($solicitud_id);
        if ($solicitud) {
            $solicitudModel->actualizarCostoEquipo($solicitud['equipo_id']);
        }
        
        $this->redirect('tecnico/mis-trabajos');
    }
    
    public function comprarExterno() {
        $this->verificarRol(['almacenista']);
        
        $solicitud_id = $_POST['solicitud_id'] ?? null;
        $proveedor = $_POST['proveedor'] ?? '';
        $precio_unitario = $_POST['precio_unitario'] ?? 0;
        
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
        
        $solicitudModel->actualizarEstado($solicitud_id, 'agotado');
        
        $this->repuestoModel->devolverStockReservado($solicitud['repuesto_id'], $solicitud['cantidad']);
        
        $compra_id = $solicitudModel->crearCompraExterna(
            $solicitud_id,
            $solicitud['equipo_id'],
            $solicitud['repuesto_id'],
            $solicitud['tecnico_id'] ?? 0,
            $solicitud['cantidad'],
            $precio_unitario,
            $proveedor
        );
        
        $solicitudModel->actualizarCompraExternaId($solicitud_id, $compra_id);
        
        require_once __DIR__ . '/../Models/Equipo.php';
        $equipoModel = new \Equipo();
        $equipo = $equipoModel->obtenerPorId($solicitud['equipo_id']);
        
        $observacion_actual = $equipo['observaciones'] ?? '';
        $nueva_observacion = $observacion_actual . "\n[" . date('d/m/Y H:i') . "] COMPRA EXTERNA: " . $solicitud['repuesto_nombre'] . " (Cant: " . $solicitud['cantidad'] . ") - Proveedor: " . $proveedor . " - Precio est: S/ " . number_format($precio_unitario, 2);
        
        $equipoModel->actualizar($solicitud['equipo_id'], ['observaciones' => trim($nueva_observacion)]);
        
        $this->redirect('pedidos/almacen');
    }
    
    public function recibirCompraExterna() {
        $this->verificarRol(['almacenista']);
        
        $compra_id = $_POST['compra_id'] ?? null;
        
        if (!$compra_id) {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        require_once __DIR__ . '/../Models/SolicitudComponente.php';
        $solicitudModel = new \SolicitudComponente();
        
        $compra = $solicitudModel->obtenerCompraExternaPorId($compra_id);
        
        if (!$compra || $compra['estado'] !== 'pendiente') {
            $this->redirect('pedidos/almacen');
            return;
        }
        
        $this->repuestoModel->actualizarStock($compra['repuesto_id'], $compra['cantidad'], 'suma');
        
        $solicitudModel->marcarCompraExternaRecibida($compra_id);
        
        $solicitudModel->actualizarEstadoSolicitud($compra['solicitud_id'], 'enviado');
        
        $this->redirect('pedidos/almacen');
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
