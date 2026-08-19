<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/captcha.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . redirigirSegunRol());
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apellido = trim($_POST['apellido'] ?? '');
    $carnet = trim($_POST['carnet'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');
    
    if (empty($apellido) || empty($carnet) || empty($password)) {
        $error = 'Complete todos los campos';
    } elseif (empty($captcha)) {
        $error = 'Ingrese el código de verificación';
    } elseif (!verificarCaptcha($captcha)) {
        $error = 'Código de verificación incorrecto';
    } else {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT id, nombre, apellido_paterno, apellido_materno, password, rol, sucursal_id FROM usuarios WHERE (apellido_paterno = ? OR apellido_materno = ?) AND carnet = ? AND activo = 1");
        $stmt->bind_param("sss", $apellido, $apellido, $carnet);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            if (password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido_paterno'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                $_SESSION['sucursal_id'] = $usuario['sucursal_id'];
                $stmt->close();
                $conn->close();
                header('Location: ' . redirigirSegunRol());
                exit;
            } else {
                $error = 'Contraseña incorrecta';
            }
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
    <style>
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .captcha-container img {
            border-radius: 8px;
            border: 2px solid var(--gris-claro);
        }
        .captcha-container input {
            flex: 1;
        }
        .captcha-refresh {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--rojo);
            padding: 5px;
        }
        .captcha-refresh:hover {
            color: var(--rojo-oscuro);
        }
    </style>
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
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
            </div>
            
            <div class="form-group">
                <label>Código de Verificación</label>
                <div class="captcha-container">
                    <img id="captcha-img" src="captcha_image.php?t=<?= time() ?>" alt="Captcha" width="150" height="50">
                    <button type="button" class="captcha-refresh" onclick="document.getElementById('captcha-img').src='captcha_image.php?t='+Date.now()" title="Actualizar código">↻</button>
                    <input type="text" name="captcha" placeholder="Código" required maxlength="5" autocomplete="off">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
