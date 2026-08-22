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
    
    if ($accion === 'cambiar_logo_empresa') {
        if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['logo_empresa']['name'], PATHINFO_EXTENSION);
            $logo_nombre = 'logo_empresa.' . $ext;
            
            if (move_uploaded_file($_FILES['logo_empresa']['tmp_name'], $upload_dir . $logo_nombre)) {
                $conn->query("UPDATE sucursales SET logo_empresa = '$logo_nombre' WHERE id = 1");
                $mensaje = 'Logo de empresa actualizado correctamente';
            } else {
                $mensaje = 'Error al subir el logo';
            }
        }
    }
    
    if ($accion === 'cambiar_logo_sucursal') {
        $sucursal_id = intval($_POST['sucursal_id']);
        
        if (isset($_FILES['logo_sucursal']) && $_FILES['logo_sucursal']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['logo_sucursal']['name'], PATHINFO_EXTENSION);
            $logo_nombre = 'sucursal_' . $sucursal_id . '.' . $ext;
            
            if (move_uploaded_file($_FILES['logo_sucursal']['tmp_name'], $upload_dir . $logo_nombre)) {
                $stmt = $conn->prepare("UPDATE sucursales SET logo = ? WHERE id = ?");
                $stmt->bind_param("si", $logo_nombre, $sucursal_id);
                
                if ($stmt->execute()) {
                    $mensaje = 'Logo de sucursal actualizado correctamente';
                } else {
                    $mensaje = 'Error al actualizar el logo';
                }
                $stmt->close();
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
        .logo-preview {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border: 2px solid var(--gris-claro);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .logo-section {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 15px;
            background: var(--gris-claro);
            border-radius: 8px;
        }
        .logo-section img {
            max-width: 150px;
            max-height: 150px;
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
                
                <div class="card">
                    <div class="card-header">
                        <h2>Logo de la Empresa</h2>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="accion" value="cambiar_logo_empresa">
                        <div class="logo-section">
                            <div>
                                <?php if ($logo_empresa && file_exists(__DIR__ . '/../../uploads/logos/' . $logo_empresa)): ?>
                                    <img src="/impMartines/uploads/logos/<?= $logo_empresa ?>" alt="Logo Empresa" class="logo-preview">
                                <?php else: ?>
                                    <div style="width: 100px; height: 100px; background: var(--gris-claro); display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                        <span style="color: var(--gris); font-size: 0.8rem;">Sin logo</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label for="logo_empresa">Cambiar Logo de Empresa</label>
                                <input type="file" id="logo_empresa" name="logo_empresa" accept="image/*" required>
                                <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 10px;">Subir Logo</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <?php foreach ($sucursales as $sucursal): ?>
                <div class="card">
                    <div class="card-header">
                        <h2><?= sanitizar($sucursal['nombre']) ?></h2>
                    </div>
                    
                    <div class="logo-section">
                        <div>
                            <?php if ($sucursal['logo'] && file_exists(__DIR__ . '/../../uploads/logos/' . $sucursal['logo'])): ?>
                                <img src="/impMartines/uploads/logos/<?= $sucursal['logo'] ?>" alt="Logo Sucursal" class="logo-preview">
                            <?php else: ?>
                                <div style="width: 100px; height: 100px; background: var(--gris-claro); display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                    <span style="color: var(--gris); font-size: 0.8rem;">Sin logo</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <form method="POST" enctype="multipart/form-data" style="margin-bottom: 15px;">
                                <input type="hidden" name="accion" value="cambiar_logo_sucursal">
                                <input type="hidden" name="sucursal_id" value="<?= $sucursal['id'] ?>">
                                <label>Cambiar Logo de Sucursal</label>
                                <input type="file" name="logo_sucursal" accept="image/*" required>
                                <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 10px;">Subir Logo</button>
                            </form>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="accion" value="editar_sucursal">
                        <input type="hidden" name="sucursal_id" value="<?= $sucursal['id'] ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombre de Sucursal</label>
                                <input type="text" name="nombre" value="<?= sanitizar($sucursal['nombre']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="tel" name="telefono" value="<?= sanitizar($sucursal['telefono'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Dirección</label>
                            <textarea name="direccion" rows="2"><?= sanitizar($sucursal['direccion'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
