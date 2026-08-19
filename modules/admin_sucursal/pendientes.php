<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['admin_sucursal']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$pendientes = $conn->query("
    SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel,
           s.nombre as sucursal_origen
    FROM equipos e
    JOIN clientes c ON e.cliente_id = c.id
    LEFT JOIN sucursales s ON e.sucursal_origen_id = s.id
    WHERE e.sucursal_actual_id = $sucursal_id AND e.estado = 'asignado_sucursal'
    AND e.id NOT IN (SELECT equipo_id FROM asignaciones_tecnico)
    ORDER BY e.fecha_registro ASC
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos Pendientes - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'pendientes'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Equipos Pendientes de Asignar'); ?>
            
            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h2>Equipos que requieren asignación a técnico</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Equipo</th>
                                    <th>Falla</th>
                                    <th>Sucursal Origen</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendientes as $p): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_registro'])) ?></td>
                                    <td><?= sanitizar($p['cliente_nombre'] . ' ' . $p['cliente_ap']) ?></td>
                                    <td><?= ucfirst($p['tipo_equipo']) ?> <?= sanitizar($p['marca']) ?> <?= sanitizar($p['modelo']) ?></td>
                                    <td><?= sanitizar(substr($p['descripcion_falla'], 0, 60)) ?></td>
                                    <td><?= sanitizar($p['sucursal_origen'] ?? 'N/A') ?></td>
                                    <td><span class="badge badge-amarillo">Pendiente</span></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($pendientes)): ?>
                                <tr><td colspan="6" style="text-align:center; padding: 20px;">No hay equipos pendientes</td></tr>
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
