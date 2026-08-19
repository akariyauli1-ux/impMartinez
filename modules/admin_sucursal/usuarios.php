<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['admin_sucursal']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre']);
    $ap = sanitizar($_POST['apellido_paterno']);
    $am = sanitizar($_POST['apellido_materno']);
    $carnet = sanitizar($_POST['carnet']);
    $email = sanitizar($_POST['email']);
    $telefono = sanitizar($_POST['telefono']);
    $rol = sanitizar($_POST['rol']);
    
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, carnet, email, telefono, rol, sucursal_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $nombre, $ap, $am, $carnet, $email, $telefono, $rol, $sucursal_id);
    if ($stmt->execute()) {
        $mensaje = 'Usuario creado correctamente';
    } else {
        $mensaje = 'Error: ' . $conn->error;
    }
    $stmt->close();
}

$usuarios = $conn->query("SELECT * FROM usuarios WHERE sucursal_id = $sucursal_id AND activo = 1 ORDER BY rol, apellido_paterno")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'usuarios'); ?>
        <div class="main-content">
            <?php renderTopbar('Gestión de Usuarios'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
                
                <div class="card">
                    <div class="card-header"><h2>Crear Nuevo Usuario</h2></div>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombre *</label>
                                <input type="text" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label>Apellido Paterno *</label>
                                <input type="text" name="apellido_paterno" required>
                            </div>
                            <div class="form-group">
                                <label>Apellido Materno</label>
                                <input type="text" name="apellido_materno">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Carnet *</label>
                                <input type="text" name="carnet" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email">
                            </div>
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="tel" name="telefono">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Rol *</label>
                            <select name="rol" required>
                                <option value="">Seleccione</option>
                                <option value="recepcionista">Recepcionista</option>
                                <option value="tecnico">Técnico</option>
                                <option value="jefe_tecnico">Jefe Técnico</option>
                                <option value="almacenista">Almacenista</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Usuarios de la Sucursal</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Nombre</th><th>Carnet</th><th>Rol</th><th>Email</th><th>Teléfono</th></tr></thead>
                            <tbody>
                                <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= sanitizar($u['nombre'] . ' ' . $u['apellido_paterno'] . ' ' . ($u['apellido_materno'] ?? '')) ?></td>
                                    <td><?= sanitizar($u['carnet']) ?></td>
                                    <td><span class="badge badge-negro"><?= ucfirst(str_replace('_', ' ', $u['rol'])) ?></span></td>
                                    <td><?= sanitizar($u['email'] ?? '-') ?></td>
                                    <td><?= sanitizar($u['telefono'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
