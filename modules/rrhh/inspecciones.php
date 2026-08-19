<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['rrhh']);

$conn = getConexion();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = intval($_POST['usuario_id']);
    $fecha = $_POST['fecha'];
    $limpieza = $_POST['limpieza'];
    $uniforme = $_POST['uniforme'];
    $observaciones = sanitizar($_POST['observaciones'] ?? '');
    
    $stmt = $conn->prepare("INSERT INTO inspecciones (usuario_id, fecha, limpieza, uniforme, observaciones, registrado_por) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $usuario_id, $fecha, $limpieza, $uniforme, $observaciones, $usuario['id']);
    if ($stmt->execute()) {
        $mensaje = 'Inspección registrada correctamente.';
    }
    $stmt->close();
}

$personal = $conn->query("SELECT id, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE activo = 1 ORDER BY apellido_paterno")->fetch_all(MYSQLI_ASSOC);
$inspecciones = $conn->query("
    SELECT i.*, u.nombre, u.apellido_paterno, s.nombre as sucursal_nombre
    FROM inspecciones i
    JOIN usuarios u ON i.usuario_id = u.id
    LEFT JOIN sucursales s ON u.sucursal_id = s.id
    ORDER BY i.fecha DESC LIMIT 50
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspecciones - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'inspecciones'); ?>
        <div class="main-content">
            <?php renderTopbar('Inspecciones de Limpieza y Uniforme'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
                
                <div class="card">
                    <div class="card-header"><h2>Registrar Inspección</h2></div>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Empleado *</label>
                                <select name="usuario_id" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($personal as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= sanitizar($p['nombre_completo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fecha *</label>
                                <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Limpieza *</label>
                                <select name="limpieza" required>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="observado">Observado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Uniforme *</label>
                                <select name="uniforme" required>
                                    <option value="completo">Completo</option>
                                    <option value="incompleto">Incompleto</option>
                                    <option value="observado">Observado</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea name="observaciones" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar Inspección</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Últimas Inspecciones</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Fecha</th><th>Empleado</th><th>Sucursal</th><th>Limpieza</th><th>Uniforme</th><th>Obs.</th></tr></thead>
                            <tbody>
                                <?php foreach ($inspecciones as $i): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($i['fecha'])) ?></td>
                                    <td><?= sanitizar($i['nombre'] . ' ' . $i['apellido_paterno']) ?></td>
                                    <td><?= sanitizar($i['sucursal_nombre'] ?? '-') ?></td>
                                    <td>
                                        <?php $b = ['aprobado'=>'badge-verde','observado'=>'badge-amarillo','rechazado'=>'badge-rojo']; ?>
                                        <span class="badge <?= $b[$i['limpieza']] ?>"><?= ucfirst($i['limpieza']) ?></span>
                                    </td>
                                    <td>
                                        <?php $b2 = ['completo'=>'badge-verde','incompleto'=>'badge-rojo','observado'=>'badge-amarillo']; ?>
                                        <span class="badge <?= $b2[$i['uniforme']] ?>"><?= ucfirst($i['uniforme']) ?></span>
                                    </td>
                                    <td><?= sanitizar($i['observaciones'] ?? '-') ?></td>
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
