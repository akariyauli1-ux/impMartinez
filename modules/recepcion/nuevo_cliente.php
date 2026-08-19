<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['recepcionista']);

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConexion();
    $nombre = sanitizar($_POST['nombre']);
    $apellido_p = sanitizar($_POST['apellido_paterno']);
    $apellido_m = sanitizar($_POST['apellido_materno']);
    $dni = sanitizar($_POST['dni']);
    $telefono = sanitizar($_POST['telefono']);
    $email = sanitizar($_POST['email']);
    $direccion = sanitizar($_POST['direccion']);
    
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, apellido_paterno, apellido_materno, dni, telefono, email, direccion) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nombre, $apellido_p, $apellido_m, $dni, $telefono, $email, $direccion);
    
    if ($stmt->execute()) {
        $mensaje = 'Cliente registrado correctamente';
    } else {
        $mensaje = 'Error al registrar el cliente';
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Cliente - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'nuevo_cliente'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Registrar Nuevo Cliente'); ?>
            
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Datos del Cliente</h2>
                    </div>
                    
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre">Nombre *</label>
                                <input type="text" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="apellido_paterno">Apellido Paterno *</label>
                                <input type="text" id="apellido_paterno" name="apellido_paterno" required>
                            </div>
                            <div class="form-group">
                                <label for="apellido_materno">Apellido Materno</label>
                                <input type="text" id="apellido_materno" name="apellido_materno">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="dni">DNI</label>
                                <input type="text" id="dni" name="dni" maxlength="20">
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono *</label>
                                <input type="tel" id="telefono" name="telefono" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea id="direccion" name="direccion" rows="2"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Registrar Cliente</button>
                        <a href="/impMartines/modules/recepcion/dashboard.php" class="btn btn-outline">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
