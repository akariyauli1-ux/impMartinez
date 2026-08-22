<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Usuario.php';

class AuthController extends Controller {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    public function login() {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect($this->obtenerRedireccion($_SESSION['usuario_rol']));
        }
        
        $this->view('auth/login', [
            'error' => '',
            'app_name' => APP_NAME
        ]);
    }
    
    public function authenticate() {
        $apellido = trim($_POST['apellido'] ?? '');
        $carnet = trim($_POST['carnet'] ?? '');
        $password = $_POST['password'] ?? '';
        $captcha = trim($_POST['captcha'] ?? '');
        
        if (empty($apellido) || empty($carnet) || empty($password)) {
            $this->view('auth/login', ['error' => 'Complete todos los campos']);
            return;
        }
        
        if (empty($captcha)) {
            $this->view('auth/login', ['error' => 'Ingrese el codigo de verificacion']);
            return;
        }
        
        if (!$this->verificarCaptcha($captcha)) {
            $this->view('auth/login', ['error' => 'Codigo de verificacion incorrecto']);
            return;
        }
        
        $usuario = $this->usuarioModel->obtenerPorApellidoYCarnet($apellido, $carnet);
        
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $this->view('auth/login', ['error' => 'Credenciales incorrectas']);
            return;
        }
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido_paterno'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['sucursal_id'] = $usuario['sucursal_id'];
        
        $this->redirect($this->obtenerRedireccion($usuario['rol']));
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('');
    }
    
    public function captcha() {
        $codigo = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
        $_SESSION['captcha_codigo'] = $codigo;
        
        $ancho = 150;
        $alto = 50;
        $imagen = imagecreatetruecolor($ancho, $alto);
        
        $color_fondo = imagecolorallocate($imagen, 245, 245, 245);
        $color_texto = imagecolorallocate($imagen, 211, 47, 47);
        $color_linea = imagecolorallocate($imagen, 180, 180, 180);
        
        imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $color_fondo);
        
        for ($i = 0; $i < 5; $i++) {
            imageline($imagen, rand(0, $ancho), rand(0, $alto), rand(0, $ancho), rand(0, $alto), $color_linea);
        }
        
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($imagen, rand(0, $ancho), rand(0, $alto), $color_linea);
        }
        
        $fuente = 'C:/Windows/Fonts/arial.ttf';
        $x = 15;
        for ($i = 0; $i < strlen($codigo); $i++) {
            $angulo = rand(-15, 15);
            $y = rand(30, 40);
            $tamano = rand(18, 22);
            
            if (file_exists($fuente)) {
                imagettftext($imagen, $tamano, $angulo, $x, $y, $color_texto, $fuente, $codigo[$i]);
            } else {
                imagestring($imagen, 5, $x, $y - 15, $codigo[$i], $color_texto);
            }
            $x += 25;
        }
        
        header('Content-Type: image/png');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        imagepng($imagen);
        imagedestroy($imagen);
        exit;
    }
    
    private function verificarCaptcha($codigo_ingresado) {
        if (!isset($_SESSION['captcha_codigo'])) {
            return false;
        }
        $valido = strtoupper($codigo_ingresado) === strtoupper($_SESSION['captcha_codigo']);
        unset($_SESSION['captcha_codigo']);
        return $valido;
    }
    
    private function obtenerRedireccion($rol) {
        $rutas = [
            'recepcionista' => 'recepcion',
            'tecnico' => 'tecnico',
            'admin_sucursal' => 'admin-sucursal',
            'jefe_tecnico' => 'jefe-tecnico',
            'almacenista' => 'almacen',
            'gerente' => 'gerente',
            'rrhh' => 'rrhh'
        ];
        return $rutas[$rol] ?? '';
    }
}
