<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';

$usuario = verificarRol(['gerente']);

$conn = getConexion();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'editar_sucursal') {
        $sucursal_id = intval($_POST['sucursal_id']);
        $nombre = sanitizar($_POST['nombre']);
        $direccion = sanitizar($_POST['direccion']);
        $telefono = sanitizar($_POST['telefono']);
        
        $stmt = $conn->prepare("UPDATE sucursales SET nombre = ?, direccion = ?, telefono = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $direccion, $telefono, $sucursal_id);
        
        if ($stmt->execute()) {
            $mensaje = 'Sucursal actualizada correctamente';
        } else {
            $mensaje = 'Error al actualizar la sucursal';
        }
        $stmt->close();
    }
    
    if ($accion === 'cambiar_logo') {
        if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['logo_empresa']['name'], PATHINFO_EXTENSION);
            $logo_nombre = 'logo_empresa.' . $ext;
            
            if (move_uploaded_file($_FILES['logo_empresa']['tmp_name'], $upload_dir . $logo_nombre)) {
                $conn->query("UPDATE sucursales SET logo_empresa = '$logo_nombre' WHERE id = 1");
                $mensaje = 'Logo actualizado correctamente';
            } else {
                $mensaje = 'Error al subir el logo';
            }
        }
    }
}

$sucursales = $conn->query("SELECT * FROM sucursales WHERE activo = 1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$logo_empresa_data = $conn->query("SELECT logo_empresa FROM sucursales WHERE id = 1")->fetch_assoc();
$logo_empresa = $logo_empresa_data['logo_empresa'] ?? null;
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sucursales - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
    <style>
        .logo-bar {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: var(--blanco);
            border-radius: 12px;
            box-shadow: var(--sombra);
            margin-bottom: 20px;
        }
        .logo-bar img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border: 2px solid var(--gris-claro);
            border-radius: 8px;
        }
        .logo-bar .no-logo {
            width: 100px;
            height: 100px;
            background: var(--blanco);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed var(--gris);
            border-radius: 8px;
        }
        .logo-bar .info {
            flex: 1;
        }
        .logo-bar .info p {
            color: var(--gris);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .table-editable input,
        .table-editable textarea {
            border: 1px solid var(--gris-claro);
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 0.9rem;
            width: 100%;
            font-family: inherit;
        }
        .table-editable input:focus,
        .table-editable textarea:focus {
            outline: none;
            border-color: var(--rojo);
        }
        .table-editable textarea {
            resize: vertical;
            min-height: 40px;
        }
    </style>
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'sucursales'); ?>
        <div class="main-content">
            <?php renderTopbar('Gestión de Sucursales'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <div class="logo-bar">
                    <div>
                        <?php if ($logo_empresa && file_exists(__DIR__ . '/../../uploads/logos/' . $logo_empresa)): ?>
                            <img src="/impMartines/uploads/logos/<?= $logo_empresa ?>?t=<?= time() ?>" alt="Logo">
                        <?php else: ?>
                            <div class="no-logo">
                                <span style="color: var(--gris); font-size: 0.75rem; text-align: center;">Sin logo</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="info">
                        <h3>Logo de la Empresa</h3>
                        <p>Este logo se muestra en todas las sucursales y en el sistema.</p>
                        <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center;">
                            <input type="hidden" name="accion" value="cambiar_logo">
                            <input type="file" name="logo_empresa" accept="image/*" required>
                            <button type="submit" class="btn btn-primary btn-sm">Subir Logo</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Sucursales</h2>
                    </div>
                    <div class="table-container">
                        <table class="table-editable">
                            <thead>
                                <tr>
                                    <th>Sucursal</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sucursales as $sucursal): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="accion" value="editar_sucursal">
                                        <input type="hidden" name="sucursal_id" value="<?= $sucursal['id'] ?>">
                                        <td>
                                            <input type="text" name="nombre" value="<?= sanitizar($sucursal['nombre']) ?>" required>
                                        </td>
                                        <td>
                                            <textarea name="direccion" rows="2"><?= sanitizar($sucursal['direccion'] ?? '') ?></textarea>
                                        </td>
                                        <td>
                                            <input type="tel" name="telefono" value="<?= sanitizar($sucursal['telefono'] ?? '') ?>">
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                        </td>
                                    </form>
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
