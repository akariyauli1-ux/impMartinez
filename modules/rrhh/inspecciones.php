<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['rrhh']);

$conn = getConexion();
$fecha_filtro = $_GET['fecha'] ?? date('Y-m-d');
$sucursal_filtro = $_GET['sucursal'] ?? '';

$where = "WHERE i.fecha = '$fecha_filtro'";
if ($sucursal_filtro) {
    $sucursal_filtro = intval($sucursal_filtro);
    $where .= " AND u.sucursal_id = $sucursal_filtro";
}

$inspecciones = $conn->query("
    SELECT i.*, u.nombre, u.apellido_paterno, u.rol, s.nombre as sucursal_nombre
    FROM inspecciones i
    JOIN usuarios u ON i.usuario_id = u.id
    LEFT JOIN sucursales s ON u.sucursal_id = s.id
    $where
    ORDER BY s.nombre, u.apellido_paterno
")->fetch_all(MYSQLI_ASSOC);

$sucursales = $conn->query("SELECT id, nombre FROM sucursales WHERE activo = 1")->fetch_all(MYSQLI_ASSOC);

$total = count($inspecciones);
$limpieza_ok = count(array_filter($inspecciones, fn($i) => $i['limpieza'] === 'aprobado'));
$uniforme_ok = count(array_filter($inspecciones, fn($i) => $i['uniforme'] === 'completo'));

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Inspecciones - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'inspecciones'); ?>
        <div class="main-content">
            <?php renderTopbar('Reporte de Inspecciones - Todas las Sucursales'); ?>
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $total ?></div>
                        <div class="stat-label">Total Inspecciones</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $limpieza_ok ?></div>
                        <div class="stat-label">Limpieza Aprobada</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $uniforme_ok ?></div>
                        <div class="stat-label">Uniforme Completo</div>
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
                        <h2>Inspecciones - <?= date('d/m/Y', strtotime($fecha_filtro)) ?></h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Cargo</th>
                                    <th>Sucursal</th>
                                    <th>Limpieza</th>
                                    <th>Hora Limpieza</th>
                                    <th>Uniforme</th>
                                    <th>Hora Uniforme</th>
                                    <th>Observaciones</th>
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
                                foreach ($inspecciones as $i): 
                                ?>
                                <tr>
                                    <td><strong><?= sanitizar($i['nombre'] . ' ' . $i['apellido_paterno']) ?></strong></td>
                                    <td><span class="badge badge-negro"><?= $rol_labels[$i['rol']] ?? $i['rol'] ?></span></td>
                                    <td><?= sanitizar($i['sucursal_nombre'] ?? '-') ?></td>
                                    <td>
                                        <?php $b = ['aprobado'=>'badge-verde','observado'=>'badge-amarillo','rechazado'=>'badge-rojo']; ?>
                                        <span class="badge <?= $b[$i['limpieza']] ?? 'badge-gris' ?>"><?= ucfirst($i['limpieza']) ?></span>
                                    </td>
                                    <td><?= $i['hora_revision_limpieza'] ? date('H:i', strtotime($i['hora_revision_limpieza'])) : '-' ?></td>
                                    <td>
                                        <?php $b2 = ['completo'=>'badge-verde','incompleto'=>'badge-rojo','observado'=>'badge-amarillo']; ?>
                                        <span class="badge <?= $b2[$i['uniforme']] ?? 'badge-gris' ?>"><?= ucfirst($i['uniforme']) ?></span>
                                    </td>
                                    <td><?= $i['hora_revision_uniforme'] ? date('H:i', strtotime($i['hora_revision_uniforme'])) : '-' ?></td>
                                    <td><?= sanitizar($i['observaciones'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($inspecciones)): ?>
                                <tr><td colspan="8" style="text-align:center; padding: 20px;">No hay registros para esta fecha</td></tr>
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
