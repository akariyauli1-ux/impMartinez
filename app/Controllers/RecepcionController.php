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
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->clienteModel = new Cliente();
        $this->equipoFotoModel = new EquipoFoto();
        $this->verificarSesion();
        $this->verificarRol(['recepcionista']);
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
            $cliente_id = $_POST['cliente_id'];
        }
        
        $data = [
            'cliente_id' => $cliente_id,
            'tipo_equipo' => $_POST['tipo_equipo'],
            'marca' => $_POST['marca'] ?? '',
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
            'fotos' => '[]',
            'estado' => 'pendiente_asignacion',
            'recepcionista_id' => $_SESSION['usuario_id'],
            'sucursal_origen_id' => $_SESSION['sucursal_id'],
            'sucursal_actual_id' => $_SESSION['sucursal_id']
        ];
        
        $equipo_id = $this->equipoModel->crear($data);
        
        if (!empty($_FILES['fotos']['name'][0])) {
            $orden = 0;
            for ($i = 0; $i < count($_FILES['fotos']['name']); $i++) {
                if ($_FILES['fotos']['error'][$i] === 0) {
                    $foto_data = file_get_contents($_FILES['fotos']['tmp_name'][$i]);
                    $foto_tipo = $_FILES['fotos']['type'][$i];
                    $this->equipoFotoModel->guardar($equipo_id, $foto_data, $foto_tipo, $orden++);
                }
            }
        }
        
        $this->redirect('recepcion/nuevo-equipo');
    }
    
    public function misRegistros() {
        $registros = $this->equipoModel->obtenerRegistradosPor($_SESSION['usuario_id']);
        
        $this->view('recepcion/mis_registros', [
            'usuario' => $this->obtenerUsuarioActual(),
            'registros' => $registros
        ]);
    }
    
    private function obtenerUsuarioActual() {
        $usuarioModel = new Usuario();
        return $usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
