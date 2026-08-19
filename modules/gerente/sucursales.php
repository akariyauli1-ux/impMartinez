<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['gerente']);

$conn = getConexion();
$sucursales = $conn->query("
    SELECT s.*, 
           COUNT(DISTINCT e.id) as total_equipos,
           SUM(CASE WHEN e.estado = 'en_reparacion' THEN 1 ELSE 0 END) as en_reparacion,
           SUM(CASE WHEN e.estado IN ('completado', 'entregado') THEN 1 ELSE 0 END) as completados,
           SUM(CASE WHEN e.estado IN ('registrado', 'pendiente_asignacion') THEN 1 ELSE 0 END) as pendientes,
           COUNT(DISTINCT u.id) as total_personal
    FROM sucursales s
    LEFT JOIN equipos e ON e.sucursal_actual_id = s.id
    LEFT JOIN usuarios u ON u.sucursal_id = s.id AND u.activo = 1
    WHERE s.activo = 1
    GROUP BY s.id
    ORDER BY s.nombre
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'sucursales'); ?>
        <div class="main-content">
            <?php renderTopbar('Vista de Sucursales'); ?>
            <div class="content-area">
                <div class="stats-grid">
                    <?php foreach ($sucursales as $s): ?>
                    <div class="card" style="margin: 0;">
                        <h3 style="color: var(--rojo); margin-bottom: 15px;"><?= sanitizar($s['nombre']) ?></h3>
                        <p style="font-size: 0.85rem; color: var(--gris); margin-bottom: 10px;"><?= sanitizar($s['direccion'] ?? '') ?></p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div><strong><?= $s['total_equipos'] ?></strong><br><small>Equipos</small></div>
                            <div><strong><?= $s['en_reparacion'] ?></strong><br><small>En Reparación</small></div>
                            <div><strong><?= $s['completados'] ?></strong><br><small>Completados</small></div>
                            <div><strong><?= $s['pendientes'] ?></strong><br><small>Pendientes</small></div>
                            <div><strong><?= $s['total_personal'] ?></strong><br><small>Personal</small></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
