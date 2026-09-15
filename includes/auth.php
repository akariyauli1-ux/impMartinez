<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /laboratorioIA/index.php');
        exit;
    }
}

function obtenerUsuarioActual() {
    if (!isset($_SESSION['usuario_id'])) return null;
    
    $conn = getConexion();
    $stmt = $conn->prepare("SELECT u.*, s.nombre as sucursal_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id WHERE u.id = ? AND u.activo = 1");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    
    return $result->fetch_assoc();
}

function verificarRol($roles_permitidos) {
    $usuario = obtenerUsuarioActual();
    if (!$usuario || !in_array($usuario['rol'], $roles_permitidos)) {
        header('Location: /laboratorioIA/dashboard.php?error=acceso');
        exit;
    }
    return $usuario;
}

function redirigirSegunRol() {
    $usuario = obtenerUsuarioActual();
    if (!$usuario) return '/laboratorioIA/index.php';
    
    $rutas = [
        'recepcionista' => '/laboratorioIA/modules/recepcion/dashboard.php',
        'tecnico' => '/laboratorioIA/modules/tecnico/dashboard.php',
        'admin_sucursal' => '/laboratorioIA/modules/admin_sucursal/dashboard.php',
        'jefe_tecnico' => '/laboratorioIA/modules/jefe_tecnico/dashboard.php',
        'almacenista' => '/laboratorioIA/modules/almacen/dashboard.php',
        'gerente' => '/laboratorioIA/modules/gerente/dashboard.php',
        'rrhh' => '/laboratorioIA/modules/rrhh/dashboard.php'
    ];
    
    return $rutas[$usuario['rol']] ?? '/laboratorioIA/index.php';
}

function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}
?>
