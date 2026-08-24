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
        $usuarios = $this->usuarioModel->obtenerTodosIncluyendoInactivos();
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('usuarios/index', [
            'usuario' => $this->obtenerUsuarioActual(),
            'usuarios' => $usuarios,
            'sucursales' => $sucursales
        ]);
    }
    
    public function guardar() {
        $foto_nombre = null;
        $foto_data = null;
        $foto_tipo = null;
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nombre = uniqid('foto_') . '.' . $ext;
            $foto_data = file_get_contents($_FILES['foto']['tmp_name']);
            $foto_tipo = $_FILES['foto']['type'];
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
            'foto' => $foto_nombre,
            'foto_data' => $foto_data,
            'foto_tipo' => $foto_tipo,
            'registrado_por' => $_SESSION['usuario_id']
        ];
        
        $this->usuarioModel->crear($data);
        $this->redirect('usuarios');
    }
    
    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('usuarios');
            return;
        }
        
        $usuario_editar = $this->usuarioModel->obtenerPorId($id);
        if (!$usuario_editar) {
            $this->redirect('usuarios');
            return;
        }
        
        $sucursales = $this->sucursalModel->obtenerTodas();
        
        $this->view('usuarios/editar', [
            'usuario' => $this->obtenerUsuarioActual(),
            'usuario_editar' => $usuario_editar,
            'sucursales' => $sucursales
        ]);
    }
    
    public function actualizar() {
        $id = $_POST['id'];
        
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? '',
            'carnet' => $_POST['carnet'],
            'email' => $_POST['email'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'rol' => $_POST['rol'],
            'sucursal_id' => $_POST['sucursal_id']
        ];
        
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nombre = uniqid('foto_') . '.' . $ext;
            $foto_data = file_get_contents($_FILES['foto']['tmp_name']);
            $foto_tipo = $_FILES['foto']['type'];
            
            $data['foto'] = $foto_nombre;
            $data['foto_data'] = $foto_data;
            $data['foto_tipo'] = $foto_tipo;
        }
        
        $this->usuarioModel->actualizar($id, $data);
        $this->redirect('usuarios');
    }
    
    public function toggleEstado() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->usuarioModel->toggleEstado($id);
        }
        $this->redirect('usuarios');
    }
    
    private function obtenerUsuarioActual() {
        return $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
    }
}
