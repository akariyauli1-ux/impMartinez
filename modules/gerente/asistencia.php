<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['gerente']);

$conn = getConexion();
$fecha_filtro = $_GET['fecha'] ?? date('Y-m-d');
$sucursal_filtro = $_GET['sucursal'] ?? '';

$where = "WHERE a.fecha = '$fecha_filtro'";
if ($sucursal_filtro) {
    $sucursal_filtro = intval($sucursal_filtro);
    $where .= " AND u.sucursal_id = $sucursal_filtro";
}

$asistencias = $conn->query("
    SELECT a.*, u.nombre, u.apellido_paterno, u.rol, s.nombre as sucursal_nombre,
           CONCAT(reg.nombre, ' ', reg.apellido_paterno) as registrado_por_nombre
    FROM asistencia a
    JOIN usuarios u ON a.usuario_id = u.id
    LEFT JOIN sucursales s ON u.sucursal_id = s.id
    LEFT JOIN usuarios reg ON a.registrado_por = reg.id
    $where
    ORDER BY s.nombre, u.apellido_paterno
")->fetch_all(MYSQLI_ASSOC);

$sucursales = $conn->query("SELECT id, nombre FROM sucursales WHERE activo = 1")->fetch_all(MYSQLI_ASSOC);

$total = count($asistencias);
$presentes = count(array_filter($asistencias, fn($a) => $a['estado'] === 'presente'));
$tardanzas = count(array_filter($asistencias, fn($a) => $a['estado'] === 'tardanza'));
$ausentes = count(array_filter($asistencias, fn($a) => $a['estado'] === 'ausente'));

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Asistencia - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'asistencia'); ?>
        <div class="main-content">
            <?php renderTopbar('Reporte de Asistencia - Vista Gerencial'); ?>
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $total ?></div>
                        <div class="stat-label">Total Registros</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $presentes ?></div>
                        <div class="stat-label">Presentes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $tardanzas ?></div>
                        <div class="stat-label">Tardanzas</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $ausentes ?></div>
                        <div class="stat-label">Ausentes</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Filtrar Reporte</h2>
                    </div>
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
                    <div class="card-header">
                        <h2>Asistencia - <?= date('d/m/Y', strtotime($fecha_filtro)) ?></h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Cargo</th>
                                    <th>Sucursal</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Estado</th>
                                    <th>Registrado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rol_labels = [
                                    'tecnico' => 'Técnico',
                                    'recepcionista' => 'Recepcionista',
                                    'almacenista' => 'Almacenista',
                                    'jefe_tecnico' => 'Jefe Técnico',
                                    'admin_sucursal' => 'Admin. Sucursal'
                                ];
                                foreach ($asistencias as $a): 
                                ?>
                                <tr>
                                    <td><strong><?= sanitizar($a['nombre'] . ' ' . $a['apellido_paterno']) ?></strong></td>
                                    <td><span class="badge badge-negro"><?= $rol_labels[$a['rol']] ?? $a['rol'] ?></span></td>
                                    <td><?= sanitizar($a['sucursal_nombre'] ?? '-') ?></td>
                                    <td><?= $a['hora_entrada'] ? date('H:i', strtotime($a['hora_entrada'])) : '-' ?></td>
                                    <td><?= $a['hora_salida'] ? date('H:i', strtotime($a['hora_salida'])) : '-' ?></td>
                                    <td>
                                        <?php
                                        $badges = ['presente' => 'badge-verde', 'tardanza' => 'badge-amarillo', 'ausente' => 'badge-rojo', 'permiso' => 'badge-gris'];
                                        ?>
                                        <span class="badge <?= $badges[$a['estado']] ?? 'badge-gris' ?>"><?= ucfirst($a['estado']) ?></span>
                                    </td>
                                    <td><?= sanitizar($a['registrado_por_nombre'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($asistencias)): ?>
                                <tr><td colspan="7" style="text-align:center; padding: 20px;">No hay registros para esta fecha</td></tr>
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
