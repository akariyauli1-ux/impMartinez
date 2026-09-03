<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../Models/EquipoFoto.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Sucursal.php';

class RecepcionController extends Controller {
    private $equipoModel;
    private $clienteModel;
    private $equipoFotoModel;
    private $usuarioModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->clienteModel = new Cliente();
        $this->equipoFotoModel = new EquipoFoto();
        $this->usuarioModel = new Usuario();
        
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, 'verificar-entrega') === false && strpos($uri, 'verificar-qr') === false) {
            $this->verificarSesion();
            $this->verificarRol(['recepcionista']);
        }
    }
    
    private function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('');
        }
    }
    
    public function dashboard() {
        $hoy = $this->equipoModel->obtenerCountPorFecha(date('Y-m-d'), $_SESSION['sucursal_id']);
        $mis_registros = $this->equipoModel->obtenerCountPorEstado('registrado', $_SESSION['sucursal_id']);
        $pendientes = $this->equipoModel->obtenerCountPorEstado('pendiente_asignacion', $_SESSION['sucursal_id']);
        
        $this->view('recepcion/dashboard', [
            'usuario' => $this->obtenerUsuarioActual(),
            'hoy' => $hoy,
            'mis_registros' => $mis_registros,
            'pendientes' => $pendientes
        ]);
    }
    
    public function nuevoCliente() {
        $this->view('recepcion/nuevo_cliente', [
            'usuario' => $this->obtenerUsuarioActual()
        ]);
    }
    
    public function guardarCliente() {
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? '',
            'dni' => $_POST['dni'] ?? '',
            'telefono' => $_POST['telefono'],
            'email' => $_POST['email'] ?? '',
            'direccion' => $_POST['direccion'] ?? ''
        ];
        
        $this->clienteModel->crear($data);
        $this->redirect('recepcion/nuevo-cliente');
    }
    
    public function nuevoEquipo() {
        $clientes = $this->clienteModel->obtenerTodos();
        
        $this->view('recepcion/nuevo_registro', [
            'usuario' => $this->obtenerUsuarioActual(),
            'clientes' => $clientes
        ]);
    }
    
    public function guardarEquipo() {
        // Determinar si es cliente existente o nuevo
        $cliente_option = $_POST['cliente_option'] ?? 'existente';
        
        if ($cliente_option === 'nuevo') {
            // Crear nuevo cliente
            $cliente_data = [
                'nombre' => $_POST['cliente_nombre'],
                'apellido_paterno' => $_POST['cliente_apellido_paterno'],
                'apellido_materno' => $_POST['cliente_apellido_materno'] ?? '',
                'dni' => $_POST['cliente_dni'] ?? '',
                'telefono' => $_POST['cliente_telefono'],
                'email' => $_POST['cliente_email'] ?? '',
                'direccion' => $_POST['cliente_direccion'] ?? ''
            ];
            
            $cliente_id = $this->clienteModel->crear($cliente_data);
        } else {
            // Usar cliente existente
            if (!isset($_POST['cliente_id']) || empty($_POST['cliente_id'])) {
                $this->redirect('recepcion/nuevo-equipo?error=cliente_no_seleccionado');
                return;
            }
            $cliente_id = $_POST['cliente_id'];
        }
        
        // Determinar la marca
        $tipo_equipo = $_POST['tipo_equipo'];
        if ($tipo_equipo === 'otro') {
            // Si es otro tipo, la marca viene del campo de texto (marca_input)
            $marca = $_POST['marca'] ?? '';
        } elseif (isset($_POST['marca']) && $_POST['marca'] === 'otra') {
            // Si seleccionó "Otra" en el select, usar el campo de texto marca_otra
            $marca = $_POST['marca_otra'] ?? '';
        } else {
            // Usar el valor del select
            $marca = $_POST['marca'] ?? '';
        }
        
        $data = [
            'cliente_id' => $cliente_id,
            'tipo_equipo' => $tipo_equipo,
            'marca' => $marca,
            'modelo' => $_POST['modelo'] ?? '',
            'numero_serie' => $_POST['numero_serie'] ?? '',
            'accesorios' => $_POST['accesorios'] ?? '',
            'descripcion_falla' => $_POST['descripcion_falla'],
            'estado_pantalla' => $_POST['estado_pantalla'] ?? null,
            'estado_carga' => $_POST['estado_carga'] ?? null,
            'estado_puertos' => $_POST['estado_puertos'] ?? null,
            'estado_case' => $_POST['estado_case'] ?? null,
            'estado_touch' => $_POST['estado_touch'] ?? null,
            'estado_camara' => $_POST['estado_camara'] ?? null,
            'estado_encendido' => $_POST['estado_encendido'] ?? null,
            'marco_doblado' => $_POST['marco_doblado'] ?? null,
            'estado_parlantes' => $_POST['estado_parlantes'] ?? null,
            'estado_imagenes' => $_POST['estado_imagenes'] ?? null,
            'previamente_abierto' => $_POST['previamente_abierto'] ?? null,
            'contacto_liquidos' => $_POST['contacto_liquidos'] ?? null,
            'equipo_reacondicionado' => $_POST['equipo_reacondicionado'] ?? null,
            'costo_estimado' => $_POST['costo_estimado'] ?? null,
            'fecha_estimada_entrega' => $_POST['fecha_estimada_entrega'] ?? null,
            'observaciones' => $_POST['observaciones'] ?? null,
            'firma_digital' => $_POST['firma_digital'] ?? null,
            'fotos' => '[]',
            'estado' => 'pendiente_asignacion',
            'recepcionista_id' => $_SESSION['usuario_id'],
            'sucursal_origen_id' => $_SESSION['sucursal_id'],
            'sucursal_actual_id' => $_SESSION['sucursal_id']
        ];
        
        $equipo_id = $this->equipoModel->crear($data);
        
        // Guardar foto del anverso
        if (isset($_FILES['foto_anverso']) && $_FILES['foto_anverso']['error'] === UPLOAD_ERR_OK) {
            $foto_data = file_get_contents($_FILES['foto_anverso']['tmp_name']);
            $foto_tipo = $_FILES['foto_anverso']['type'];
            $this->equipoFotoModel->guardar($equipo_id, $foto_data, $foto_tipo, 0, 'anverso');
        }
        
        // Guardar foto del reverso
        if (isset($_FILES['foto_reverso']) && $_FILES['foto_reverso']['error'] === UPLOAD_ERR_OK) {
            $foto_data = file_get_contents($_FILES['foto_reverso']['tmp_name']);
            $foto_tipo = $_FILES['foto_reverso']['type'];
            $this->equipoFotoModel->guardar($equipo_id, $foto_data, $foto_tipo, 1, 'reverso');
        }
        
        // Redirigir al recibo del equipo registrado
        $this->redirect('recepcion/ver-recibo?id=' . $equipo_id);
    }
    
    public function verRecibo() {
        $equipo_id = $_GET['id'] ?? null;
        
        if (!$equipo_id) {
            $this->redirect('recepcion/nuevo-equipo');
            return;
        }
        
        // Obtener datos del equipo con información del cliente
        $equipo = $this->equipoModel->obtenerPorId($equipo_id);
        
        if (!$equipo) {
            $this->redirect('recepcion/nuevo-equipo');
            return;
        }
        
        // Obtener datos del cliente
        $cliente = $this->clienteModel->obtenerPorId($equipo['cliente_id']);
        
        // Obtener datos del recepcionista
        $recepcionista = $this->obtenerUsuarioActual();
        
        // Obtener datos de la sucursal
        $sucursalModel = new Sucursal();
        $sucursal = $sucursalModel->obtenerPorId($_SESSION['sucursal_id']);
        
        // Obtener datos del administrador de la sucursal
        $admin_sucursal = $this->usuarioModel->obtenerAdminSucursal($_SESSION['sucursal_id']);
        
        // Obtener fotos del equipo
        $fotos = $this->equipoFotoModel->obtenerPorEquipo($equipo_id);
        
        $this->view('recepcion/recibo', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipo' => $equipo,
            'cliente' => $cliente,
            'recepcionista' => $recepcionista,
            'sucursal' => $sucursal,
            'admin_sucursal' => $admin_sucursal,
            'fotos' => $fotos
        ]);
    }
    
    public function misRegistros() {
        $registros = $this->equipoModel->obtenerRegistradosPor($_SESSION['usuario_id']);
        
        $this->view('recepcion/mis_registros', [
            'usuario' => $this->obtenerUsuarioActual(),
            'registros' => $registros
        ]);
    }
    
    public function equiposListos() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $equipos = $this->equipoModel->obtenerCompletadosPorSucursal($sucursal_id);
        
        $equipos_con_componentes = [];
        foreach ($equipos as $equipo) {
            $equipo['componentes'] = $this->equipoModel->obtenerComponentesPorEquipo($equipo['id']);
            $equipos_con_componentes[] = $equipo;
        }
        
        $this->view('recepcion/equipos_listos', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipos' => $equipos_con_componentes
        ]);
    }
    
    public function marcarEntregado() {
        $equipo_id = $_POST['equipo_id'];
        
        $this->equipoModel->actualizar($equipo_id, [
            'estado' => 'entregado',
            'entregado_por' => $_SESSION['usuario_id'],
            'fecha_entrega' => date('Y-m-d H:i:s')
        ]);
        
        $this->redirect('recepcion/equipos-listos');
    }
    
    public function historialEntregas() {
        $sucursal_id = $_SESSION['sucursal_id'];
        $entregas = $this->equipoModel->obtenerEntregadosPorSucursal($sucursal_id);
        
        $this->view('recepcion/historial_entregas', [
            'usuario' => $this->obtenerUsuarioActual(),
            'entregas' => $entregas
        ]);
    }
    
    public function formularioEntrega() {
        $equipo_id = $_GET['id'] ?? null;
        
        if (!$equipo_id) {
            $this->redirect('recepcion/equipos-listos');
            return;
        }
        
        $equipo = $this->equipoModel->obtenerPorId($equipo_id);
        
        if (!$equipo || $equipo['estado'] !== 'completado') {
            $this->redirect('recepcion/equipos-listos');
            return;
        }
        
        $componentes = $this->equipoModel->obtenerComponentesPorEquipo($equipo_id);
        
        $this->view('recepcion/entrega', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipo' => $equipo,
            'componentes' => $componentes
        ]);
    }
    
    public function procesarEntrega() {
        $equipo_id = $_POST['equipo_id'];
        $costo_final = $_POST['costo_final'];
        $firma_entrega = $_POST['firma_entrega'];
        
        // Convertir base64 a imagen
        $firma_data = null;
        if (preg_match('/^data:image\/(\w+);base64,/', $firma_entrega, $type)) {
            $firma_entrega = substr($firma_entrega, strpos($firma_entrega, ',') + 1);
            $firma_data = base64_decode($firma_entrega);
        }
        
        // Generar hash de seguridad único
        $hash_seguridad = hash('sha256', $equipo_id . time() . random_int(100000, 999999) . $_SESSION['usuario_id'] . 'impmartinez_salt_2024');
        
        $this->equipoModel->actualizar($equipo_id, [
            'estado' => 'entregado',
            'costo_final' => $costo_final,
            'firma_entrega' => $firma_data,
            'fecha_entrega' => date('Y-m-d H:i:s'),
            'fecha_conformidad_entrega' => date('Y-m-d H:i:s'),
            'entregado_por' => $_SESSION['usuario_id'],
            'hash_seguridad' => $hash_seguridad
        ]);
        
        $this->redirect('recepcion/ver-recibo-entrega?id=' . $equipo_id);
    }
    
    public function verReciboEntrega() {
        $equipo_id = $_GET['id'] ?? null;
        
        if (!$equipo_id) {
            $this->redirect('recepcion/equipos-listos');
            return;
        }
        
        $equipo = $this->equipoModel->obtenerPorId($equipo_id);
        
        if (!$equipo) {
            $this->redirect('recepcion/equipos-listos');
            return;
        }
        
        // Convertir firma de entrega a base64 si existe
        if (!empty($equipo['firma_entrega']) && !is_string($equipo['firma_entrega'])) {
            $equipo['firma_entrega'] = 'data:image/png;base64,' . base64_encode($equipo['firma_entrega']);
        }
        
        // Convertir firma de recepción a base64 si existe
        if (!empty($equipo['firma_digital']) && !is_string($equipo['firma_digital'])) {
            $equipo['firma_digital'] = 'data:image/png;base64,' . base64_encode($equipo['firma_digital']);
        }
        
        $cliente = $this->clienteModel->obtenerPorId($equipo['cliente_id']);
        $recepcionista = $this->obtenerUsuarioActual();
        
        $sucursalModel = new Sucursal();
        $sucursal = $sucursalModel->obtenerPorId($_SESSION['sucursal_id']);
        
        // Obtener fotos del equipo
        $fotos = $this->equipoFotoModel->obtenerPorEquipo($equipo_id);
        
        // Obtener componentes usados
        $componentes = $this->equipoModel->obtenerComponentesPorEquipo($equipo_id);
        
        $this->view('recepcion/recibo_entrega', [
            'usuario' => $this->obtenerUsuarioActual(),
            'equipo' => $equipo,
            'cliente' => $cliente,
            'recepcionista' => $recepcionista,
            'sucursal' => $sucursal,
            'fotos' => $fotos,
            'componentes' => $componentes
        ]);
    }
    
    public function verificarQR() {
        $orden = $_GET['orden'] ?? null;
        
        if (!$orden) {
            echo '<h1>Orden no válida</h1>';
            exit;
        }
        
        // Extraer el ID del número de orden (formato: ORD-000001)
        $equipo_id = intval(str_replace('ORD-', '', $orden));
        
        $equipo = $this->equipoModel->obtenerPorId($equipo_id);
        
        if (!$equipo) {
            echo '<h1>Orden no encontrada</h1>';
            exit;
        }
        
        $cliente = $this->clienteModel->obtenerPorId($equipo['cliente_id']);
        
        $sucursalModel = new Sucursal();
        $sucursal = $sucursalModel->obtenerPorId($equipo['sucursal_actual_id']);
        
        $this->view('recepcion/verificar_qr', [
            'equipo' => $equipo,
            'cliente' => $cliente,
            'sucursal' => $sucursal,
            'orden' => $orden
        ]);
    }
    
    public function verificarEntrega() {
        $hash = $_GET['hash'] ?? null;
        
        $resultado = null;
        
        if ($hash && strlen($hash) >= 32) {
            $sql = "SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel, c.dni as cliente_dni,
                    s.nombre as sucursal_nombre
                    FROM equipos e
                    JOIN clientes c ON e.cliente_id = c.id
                    LEFT JOIN sucursales s ON e.sucursal_actual_id = s.id
                    WHERE e.hash_seguridad = ?";
            $resultado = $this->equipoModel->fetchOne($sql, [$hash]);
        }
        
        $this->view('recepcion/verificar_entrega', [
            'resultado' => $resultado,
            'hash' => $hash
        ]);
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
