<?php
require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . redirigirSegunRol());
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apellido = trim($_POST['apellido'] ?? '');
    $carnet = trim($_POST['carnet'] ?? '');
    
    if (empty($apellido) || empty($carnet)) {
        $error = 'Complete todos los campos';
    } else {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT id, nombre, apellido_paterno, apellido_materno, rol, sucursal_id FROM usuarios WHERE (apellido_paterno = ? OR apellido_materno = ?) AND carnet = ? AND activo = 1");
        $stmt->bind_param("sss", $apellido, $apellido, $carnet);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido_paterno'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['sucursal_id'] = $usuario['sucursal_id'];
            $stmt->close();
            $conn->close();
            header('Location: ' . redirigirSegunRol());
            exit;
        } else {
            $error = 'Apellido o número de carnet incorrectos';
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpMartínez - Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <h1>ImpMartínez</h1>
            <p>Sistema de Gestión de Servicio Técnico</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= sanitizar($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" placeholder="Ingrese su apellido" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="carnet">Número de Carnet</label>
                <input type="text" id="carnet" name="carnet" placeholder="Ingrese su número de carnet" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
