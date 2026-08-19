<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['rrhh']);

$conn = getConexion();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = intval($_POST['usuario_id']);
    $fecha = $_POST['fecha'];
    $hora_entrada = $_POST['hora_entrada'] ?: null;
    $hora_salida = $_POST['hora_salida'] ?: null;
    $estado = $_POST['estado'];
    $observaciones = sanitizar($_POST['observaciones'] ?? '');
    
    $stmt = $conn->prepare("INSERT INTO asistencia (usuario_id, fecha, hora_entrada, hora_salida, estado, observaciones, registrado_por) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE hora_entrada=VALUES(hora_entrada), hora_salida=VALUES(hora_salida), estado=VALUES(estado), observaciones=VALUES(observaciones)");
    $stmt->bind_param("isssssi", $usuario_id, $fecha, $hora_entrada, $hora_salida, $estado, $observaciones, $usuario['id']);
    if ($stmt->execute()) {
        $mensaje = 'Asistencia registrada correctamente.';
    }
    $stmt->close();
}

$personal = $conn->query("SELECT id, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo, sucursal_id FROM usuarios WHERE activo = 1 ORDER BY apellido_paterno")->fetch_all(MYSQLI_ASSOC);
$sucursales = $conn->query("SELECT id, nombre FROM sucursales WHERE activo = 1")->fetch_all(MYSQLI_ASSOC);

$fecha_filtro = $_GET['fecha'] ?? date('Y-m-d');
$sucursal_filtro = $_GET['sucursal'] ?? '';

$where = "WHERE a.fecha = '$fecha_filtro'";
if ($sucursal_filtro) {
    $sucursal_filtro = intval($sucursal_filtro);
    $where .= " AND u.sucursal_id = $sucursal_filtro";
}

$asistencias = $conn->query("
    SELECT a.*, u.nombre, u.apellido_paterno, s.nombre as sucursal_nombre
    FROM asistencia a
    JOIN usuarios u ON a.usuario_id = u.id
    LEFT JOIN sucursales s ON u.sucursal_id = s.id
    $where
    ORDER BY s.nombre, u.apellido_paterno
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'asistencia'); ?>
        <div class="main-content">
            <?php renderTopbar('Registro de Asistencia'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
                
                <div class="card">
                    <div class="card-header"><h2>Filtrar Asistencia</h2></div>
                    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                        <div class="form-group" style="margin: 0;">
                            <label>Fecha</label>
                            <input type="date" name="fecha" value="<?= $fecha_filtro ?>">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label>Sucursal</label>
                            <select name="sucursal">
                                <option value="">Todas</option>
                                <?php foreach ($sucursales as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $sucursal_filtro == $s['id'] ? 'selected' : '' ?>><?= sanitizar($s['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Registrar Asistencia</h2></div>
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
                            <div class="form-group">
                                <label>Hora Entrada</label>
                                <input type="time" name="hora_entrada">
                            </div>
                            <div class="form-group">
                                <label>Hora Salida</label>
                                <input type="time" name="hora_salida">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Estado *</label>
                                <select name="estado" required>
                                    <option value="presente">Presente</option>
                                    <option value="tardanza">Tardanza</option>
                                    <option value="ausente">Ausente</option>
                                    <option value="permiso">Permiso</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Observaciones</label>
                                <input type="text" name="observaciones">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Asistencia - <?= date('d/m/Y', strtotime($fecha_filtro)) ?></h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Empleado</th><th>Sucursal</th><th>Entrada</th><th>Salida</th><th>Estado</th><th>Obs.</th></tr></thead>
                            <tbody>
                                <?php foreach ($asistencias as $a): ?>
                                <tr>
                                    <td><?= sanitizar($a['nombre'] . ' ' . $a['apellido_paterno']) ?></td>
                                    <td><?= sanitizar($a['sucursal_nombre'] ?? '-') ?></td>
                                    <td><?= $a['hora_entrada'] ? date('H:i', strtotime($a['hora_entrada'])) : '-' ?></td>
                                    <td><?= $a['hora_salida'] ? date('H:i', strtotime($a['hora_salida'])) : '-' ?></td>
                                    <td>
                                        <?php
                                        $badges = ['presente' => 'badge-verde', 'tardanza' => 'badge-amarillo', 'ausente' => 'badge-rojo', 'permiso' => 'badge-gris'];
                                        ?>
                                        <span class="badge <?= $badges[$a['estado']] ?>"><?= ucfirst($a['estado']) ?></span>
                                    </td>
                                    <td><?= sanitizar($a['observaciones'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($asistencias)): ?>
                                <tr><td colspan="6" style="text-align:center; padding: 20px;">No hay registros para esta fecha</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
