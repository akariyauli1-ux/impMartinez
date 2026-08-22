<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Equipo.php';
require_once __DIR__ . '/../Models/Cliente.php';

class RecepcionController extends Controller {
    private $equipoModel;
    private $clienteModel;
    
    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->clienteModel = new Cliente();
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
        
        $this->view('recepcion/nuevo_equipo', [
            'usuario' => $this->obtenerUsuarioActual(),
            'clientes' => $clientes
        ]);
    }
    
    public function guardarEquipo() {
        $fotos = [];
        if (!empty($_FILES['fotos']['name'][0])) {
            $upload_dir = __DIR__ . '/../../uploads/fotos_equipos/';
            for ($i = 0; $i < count($_FILES['fotos']['name']); $i++) {
                if ($_FILES['fotos']['error'][$i] === 0) {
                    $ext = pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION);
                    $nombre = uniqid('foto_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['fotos']['tmp_name'][$i], $upload_dir . $nombre)) {
                        $fotos[] = $nombre;
                    }
                }
            }
        }
        
        $data = [
            'cliente_id' => $_POST['cliente_id'],
            'tipo_equipo' => $_POST['tipo_equipo'],
            'marca' => $_POST['marca'] ?? '',
            'modelo' => $_POST['modelo'] ?? '',
            'numero_serie' => $_POST['numero_serie'] ?? '',
            'accesorios' => $_POST['accesorios'] ?? '',
            'descripcion_falla' => $_POST['descripcion_falla'],
            'fotos' => json_encode($fotos),
            'estado' => 'pendiente_asignacion',
            'recepcionista_id' => $_SESSION['usuario_id'],
            'sucursal_origen_id' => $_SESSION['sucursal_id'],
            'sucursal_actual_id' => $_SESSION['sucursal_id']
        ];
        
        $this->equipoModel->crear($data);
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
