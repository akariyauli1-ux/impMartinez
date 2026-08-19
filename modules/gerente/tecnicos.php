<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['gerente']);

$conn = getConexion();
$tecnicos = $conn->query("
    SELECT u.id, u.nombre, u.apellido_paterno, s.nombre as sucursal_nombre,
           COUNT(DISTINCT at.equipo_id) as total_asignados,
           SUM(CASE WHEN e.estado = 'completado' THEN 1 ELSE 0 END) as completados,
           SUM(CASE WHEN e.estado = 'en_reparacion' THEN 1 ELSE 0 END) as en_proceso,
           SUM(CASE WHEN e.estado NOT IN ('completado', 'entregado') THEN 1 ELSE 0 END) as atrasados
    FROM usuarios u
    LEFT JOIN sucursales s ON u.sucursal_id = s.id
    LEFT JOIN asignaciones_tecnico at ON u.id = at.tecnico_id
    LEFT JOIN equipos e ON at.equipo_id = e.id
    WHERE u.rol = 'tecnico' AND u.activo = 1
    GROUP BY u.id
    ORDER BY en_proceso DESC
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajo Técnicos - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'tecnicos'); ?>
        <div class="main-content">
            <?php renderTopbar('Trabajo Realizado y Atrasado de Técnicos'); ?>
            <div class="content-area">
                <div class="card">
                    <div class="card-header"><h2>Estado de Todos los Técnicos</h2></div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr><th>Técnico</th><th>Sucursal</th><th>Carga Actual</th><th>Completados</th><th>En Proceso</th><th>Total Asignados</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tecnicos as $t): ?>
                                <tr>
                                    <td><strong><?= sanitizar($t['nombre'] . ' ' . $t['apellido_paterno']) ?></strong></td>
                                    <td><?= sanitizar($t['sucursal_nombre'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge <?= $t['en_proceso'] >= 4 ? 'badge-rojo' : 'badge-verde' ?>">
                                            <?= $t['en_proceso'] ?>/4
                                        </span>
                                    </td>
                                    <td><span class="badge badge-verde"><?= $t['completados'] ?></span></td>
                                    <td><span class="badge badge-amarillo"><?= $t['en_proceso'] ?></span></td>
                                    <td><span class="badge badge-negro"><?= $t['total_asignados'] ?></span></td>
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
