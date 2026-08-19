<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['gerente']);

$conn = getConexion();
$admins = $conn->query("
    SELECT u.id, u.nombre, u.apellido_paterno, s.nombre as sucursal,
           (SELECT COUNT(*) FROM equipos e WHERE e.sucursal_actual_id = s.id AND e.estado = 'en_reparacion') as en_reparacion,
           (SELECT COUNT(*) FROM equipos e WHERE e.sucursal_actual_id = s.id AND e.estado IN ('completado', 'entregado')) as completados,
           (SELECT COUNT(*) FROM equipos e WHERE e.sucursal_actual_id = s.id AND e.estado IN ('registrado', 'pendiente_asignacion')) as pendientes
    FROM usuarios u
    JOIN sucursales s ON u.sucursal_id = s.id
    WHERE u.rol = 'admin_sucursal' AND u.activo = 1
    ORDER BY s.nombre
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin. Sucursales - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'administradores'); ?>
        <div class="main-content">
            <?php renderTopbar('Trabajo de Administradores de Sucursal'); ?>
            <div class="content-area">
                <div class="card">
                    <div class="card-header"><h2>Gestión por Administrador</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Administrador</th><th>Sucursal</th><th>Pendientes</th><th>En Reparación</th><th>Completados</th></tr></thead>
                            <tbody>
                                <?php foreach ($admins as $a): ?>
                                <tr>
                                    <td><strong><?= sanitizar($a['nombre'] . ' ' . $a['apellido_paterno']) ?></strong></td>
                                    <td><?= sanitizar($a['sucursal']) ?></td>
                                    <td><span class="badge badge-amarillo"><?= $a['pendientes'] ?></span></td>
                                    <td><span class="badge badge-rojo"><?= $a['en_reparacion'] ?></span></td>
                                    <td><span class="badge badge-verde"><?= $a['completados'] ?></span></td>
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
