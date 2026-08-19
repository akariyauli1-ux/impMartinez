<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['rrhh']);

$conn = getConexion();
$sucursal_filtro = $_GET['sucursal'] ?? '';
$where = "WHERE u.activo = 1 AND u.rol = 'tecnico'";
if ($sucursal_filtro) {
    $sucursal_filtro = intval($sucursal_filtro);
    $where .= " AND u.sucursal_id = $sucursal_filtro";
}

$tecnicos = $conn->query("
    SELECT u.id, u.nombre, u.apellido_paterno, s.nombre as sucursal_nombre,
           COUNT(DISTINCT at.equipo_id) as trabajos_totales,
           SUM(CASE WHEN e.estado = 'completado' THEN 1 ELSE 0 END) as completados,
           SUM(CASE WHEN e.estado = 'en_reparacion' THEN 1 ELSE 0 END) as en_proceso
    FROM usuarios u
    LEFT JOIN sucursales s ON u.sucursal_id = s.id
    LEFT JOIN asignaciones_tecnico at ON u.id = at.tecnico_id
    LEFT JOIN equipos e ON at.equipo_id = e.id
    $where
    GROUP BY u.id
    ORDER BY trabajos_totales DESC
")->fetch_all(MYSQLI_ASSOC);

$sucursales = $conn->query("SELECT id, nombre FROM sucursales WHERE activo = 1")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productividad - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'productividad'); ?>
        <div class="main-content">
            <?php renderTopbar('Productividad de Técnicos'); ?>
            <div class="content-area">
                <div class="card">
                    <div class="card-header"><h2>Filtrar por Sucursal</h2></div>
                    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
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
                    <div class="card-header"><h2>Cantidad de Trabajo por Técnico</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Técnico</th><th>Sucursal</th><th>Total Trabajos</th><th>Completados</th><th>En Proceso</th></tr></thead>
                            <tbody>
                                <?php foreach ($tecnicos as $t): ?>
                                <tr>
                                    <td><strong><?= sanitizar($t['nombre'] . ' ' . $t['apellido_paterno']) ?></strong></td>
                                    <td><?= sanitizar($t['sucursal_nombre'] ?? '-') ?></td>
                                    <td><span class="badge badge-negro"><?= $t['trabajos_totales'] ?></span></td>
                                    <td><span class="badge badge-verde"><?= $t['completados'] ?></span></td>
                                    <td><span class="badge badge-rojo"><?= $t['en_proceso'] ?></span></td>
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
