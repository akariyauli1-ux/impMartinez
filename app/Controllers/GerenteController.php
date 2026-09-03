<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Sucursal.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/AsignacionTecnico.php';
require_once __DIR__ . '/../Models/Repuesto.php';
require_once __DIR__ . '/../Models/PedidoRepuesto.php';
require_once __DIR__ . '/../Models/Asistencia.php';
require_once __DIR__ . '/../Models/Inspeccion.php';

class GerenteController extends Controller {
    private $equipoModel;
    private $sucursalModel;
    private $usuarioModel;
    private $asignacionTecnicoModel;
    private $repuestoModel;
    private $pedidoModel;
    private $asistenciaModel;
    private $inspeccionModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->sucursalModel = new Sucursal();
        $this->usuarioModel = new Usuario();
        $this->asignacionTecnicoModel = new AsignacionTecnico();
        $this->repuestoModel = new Repuesto();
        $this->pedidoModel = new PedidoRepuesto();
        $this->asistenciaModel = new Asistencia();
        $this->inspeccionModel = new Inspeccion();
        $this->verificarSesion();
        $this->verificarRol(['gerente']);
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    public function dashboard() {
        $total_equipos = $this->equipoModel->obtenerCountPorEstado('registrado');
        $en_reparacion = $this->equipoModel->obtenerCountPorEstado('en_reparacion');
        $completados = $this->equipoModel->obtenerCountPorEstado('completado');
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('gerente/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'total_equipos' => $total_equipos,
            'en_reparacion' => $en_reparacion,
            'completados' => $completados,
            'sucursales' => $sucursales
        ]);
    }
    
    public function sucursales() {
        $sucursales = $this->sucursalModel->obtenerResumen();
        $logo_empresa = $this->sucursalModel->obtenerLogoEmpresa();
        
        $this->view('gerente/sucursales', [
            'usuario' => $this->obtenerUsuarioActual(),
            'sucursales' => $sucursales,
            'logo_empresa' => $logo_empresa
        ]);
    }
    
    public function guardarSucursal() {
        $sucursal_id = $_POST['sucursal_id'];
        $data = [
            'nombre' => $_POST['nombre'],
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? ''
        ];
        
        $this->sucursalModel->actualizar($sucursal_id, $data);
        $this->redirect('gerente/sucursales');
    }
    
    public function subirLogo() {
        if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['logo_empresa']['name'], PATHINFO_EXTENSION);
            $logo_nombre = 'logo_empresa.' . $ext;
            
            if (move_uploaded_file($_FILES['logo_empresa']['tmp_name'], $upload_dir . $logo_nombre)) {
                $this->sucursalModel->actualizarLogoEmpresa($logo_nombre);
            }
        }
        
        $this->redirect('gerente/sucursales');
    }
    
    public function tecnicos() {
        $tecnicos_data = [];
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        foreach ($sucursales as $sucursal) {
            $tecnicos = $this->usuarioModel->obtenerTecnicosPorSucursal($sucursal['id']);
            foreach ($tecnicos as $tecnico) {
                $tecnico['sucursal_nombre'] = $sucursal['nombre'];
                // Obtener estadísticas de trabajos del técnico
                $estadisticas = $this->equipoModel->obtenerEstadisticasPorTecnico($tecnico['id']);
                $tecnico['estadisticas'] = $estadisticas;
                $tecnicos_data[] = $tecnico;
            }
        }
        
        $this->view('gerente/tecnicos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'tecnicos' => $tecnicos_data
        ]);
    }
    
    public function almacen() {
        $sucursales = $this->sucursalModel->obtenerTodas();
        $mas_pedidos = $this->pedidoModel->obtenerMasPedidos();
        
        $estado_almacen = [];
        foreach ($sucursales as $sucursal) {
            $repuestos = $this->repuestoModel->obtenerPorSucursal($sucursal['id']);
            $estado_almacen[] = [
                'sucursal' => $sucursal['nombre'],
                'total_repuestos' => count($repuestos),
                'stock_total' => array_sum(array_column($repuestos, 'stock')),
                'stock_bajo' => $this->repuestoModel->obtenerStockBajo($sucursal['id'])
            ];
        }
        
        $this->view('gerente/almacen', [
            'usuario' => $this->obtenerUsuarioActual(),
            'estado_almacen' => $estado_almacen,
            'mas_pedidos' => $mas_pedidos
        ]);
    }
    
    public function administradores() {
        $admins = [];
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        foreach ($sucursales as $sucursal) {
            $usuarios = $this->usuarioModel->obtenerTodosPorSucursal($sucursal['id']);
            foreach ($usuarios as $usuario) {
                if ($usuario['rol'] === 'admin_sucursal') {
                    $usuario['sucursal_nombre'] = $sucursal['nombre'];
                    $admins[] = $usuario;
                }
            }
        }
        
        $this->view('gerente/administradores', [
            'usuario' => $this->obtenerUsuarioActual(),
            'admins' => $admins
        ]);
    }
    
    public function asistencia() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $sucursal_id = $_GET['sucursal'] ?? null;
        
        $asistencias = $this->asistenciaModel->obtenerReporte($fecha, $sucursal_id);
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('gerente/asistencia', [
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
        
        $this->view('gerente/inspecciones', [
            'usuario' => $this->obtenerUsuarioActual(),
            'inspecciones' => $inspecciones,
            'fecha' => $fecha,
            'sucursales' => $sucursales
        ]);
    }
    
    public function trazabilidad() {
        $filtros = [
            'estado' => $_GET['estado'] ?? null,
            'sucursal_id' => $_GET['sucursal'] ?? null,
            'busqueda' => $_GET['busqueda'] ?? null,
            'fecha_desde' => $_GET['fecha_desde'] ?? null,
            'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
        ];
        
        $equipos = $this->equipoModel->obtenerTodosConDetalles($filtros);
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('gerente/trazabilidad', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipos' => $equipos,
            'sucursales' => $sucursales,
            'filtros' => $filtros
        ]);
    }
    
    public function trazabilidadDetalle() {
        $equipo_id = $_GET['id'] ?? null;
        
        if (!$equipo_id) {
            $this->redirect('gerente/trazabilidad');
            return;
        }
        
        $equipo = $this->equipoModel->obtenerTrazabilidadCompleta($equipo_id);
        
        if (!$equipo) {
            $this->redirect('gerente/trazabilidad');
            return;
        }
        
        $timeline = $this->equipoModel->obtenerTimelineEquipo($equipo_id);
        
        $this->view('gerente/trazabilidad_detalle', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipo' => $equipo,
            'timeline' => $timeline
        ]);
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
