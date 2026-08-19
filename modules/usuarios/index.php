<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';

$usuario = verificarRol(['gerente', 'rrhh']);

$conn = getConexion();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre']);
    $ap = sanitizar($_POST['apellido_paterno']);
    $am = sanitizar($_POST['apellido_materno']);
    $carnet = sanitizar($_POST['carnet']);
    $email = sanitizar($_POST['email']);
    $telefono = sanitizar($_POST['telefono']);
    $rol = sanitizar($_POST['rol']);
    $sucursal_id = intval($_POST['sucursal_id']);
    $password = $_POST['password'];
    
    if (strlen($password) < 6) {
        $mensaje = 'Error: La contraseña debe tener al menos 6 caracteres';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
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
        
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, carnet, email, telefono, rol, sucursal_id, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssisss", $nombre, $ap, $am, $carnet, $email, $telefono, $rol, $sucursal_id, $password_hash, $foto_nombre);
        
        if ($stmt->execute()) {
            $mensaje = 'Usuario creado correctamente';
        } else {
            $mensaje = 'Error: ' . $conn->error;
        }
        $stmt->close();
    }
}

$sucursales = $conn->query("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

if ($usuario['rol'] === 'gerente') {
    $usuarios = $conn->query("SELECT u.*, s.nombre as sucursal_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id WHERE u.activo = 1 ORDER BY u.apellido_paterno")->fetch_all(MYSQLI_ASSOC);
} else {
    $usuarios = $conn->query("SELECT u.*, s.nombre as sucursal_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id WHERE u.activo = 1 ORDER BY u.apellido_paterno")->fetch_all(MYSQLI_ASSOC);
}

$conn->close();

$roles_disponibles = [
    'recepcionista' => 'Recepcionista',
    'tecnico' => 'Técnico',
    'jefe_tecnico' => 'Jefe Técnico',
    'almacenista' => 'Almacenista',
    'admin_sucursal' => 'Administrador de Sucursal',
    'rrhh' => 'Recursos Humanos',
    'gerente' => 'Gerente'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
    <style>
        .foto-preview {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--rojo);
        }
        .foto-upload {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .foto-upload img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--rojo);
        }
    </style>
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'usuarios'); ?>
        <div class="main-content">
            <?php renderTopbar('Gestión de Usuarios'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert <?= strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success' ?>"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header"><h2>Crear Nuevo Usuario</h2></div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="foto-upload">
                            <img id="preview-foto" src="/impMartines/assets/img/default-user.png" alt="Preview">
                            <div>
                                <label for="foto">Foto del Empleado</label>
                                <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                            </div>
                        </div>
                        
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
                                <label>Contraseña *</label>
                                <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="tel" name="telefono">
                            </div>
                            <div class="form-group">
                                <label>Sucursal *</label>
                                <select name="sucursal_id" required>
                                    <option value="">Seleccione sucursal</option>
                                    <?php foreach ($sucursales as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= sanitizar($s['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cargo/Rol *</label>
                                <select name="rol" required>
                                    <option value="">Seleccione cargo</option>
                                    <?php foreach ($roles_disponibles as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Usuarios del Sistema</h2></div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre Completo</th>
                                    <th>Carnet</th>
                                    <th>Sucursal</th>
                                    <th>Cargo</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td>
                                        <?php if ($u['foto']): ?>
                                            <img src="/impMartines/uploads/fotos_usuarios/<?= $u['foto'] ?>" alt="Foto" class="foto-preview">
                                        <?php else: ?>
                                            <img src="/impMartines/assets/img/default-user.png" alt="Sin foto" class="foto-preview">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= sanitizar($u['nombre'] . ' ' . $u['apellido_paterno'] . ' ' . ($u['apellido_materno'] ?? '')) ?></td>
                                    <td><?= sanitizar($u['carnet']) ?></td>
                                    <td><?= sanitizar($u['sucursal_nombre'] ?? 'Sin asignar') ?></td>
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
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-foto').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
