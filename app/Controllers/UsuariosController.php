<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Sucursal.php';

class UsuariosController extends Controller {
    private $usuarioModel;
    private $sucursalModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->sucursalModel = new Sucursal();
        $this->verificarSesion();
        $this->verificarRol(['gerente', 'rrhh']);
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
    
    public function index() {
        $usuarios = $this->usuarioModel->obtenerTodos();
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('usuarios/index', [
            'usuario' => $this->obtenerUsuarioActual(),
            'usuarios' => $usuarios,
            'sucursales' => $sucursales
        ]);
    }
    
    public function guardar() {
        $foto_nombre = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/fotos_usuarios/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nombre = uniqid('foto_') . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_nombre);
        }
        
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? '',
            'carnet' => $_POST['carnet'],
            'email' => $_POST['email'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'rol' => $_POST['rol'],
            'sucursal_id' => $_POST['sucursal_id'],
            'password' => $password_hash,
            'foto' => $foto_nombre
        ];
        
        $this->usuarioModel->crear($data);
        $this->redirect('usuarios');
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
