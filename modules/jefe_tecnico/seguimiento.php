<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['jefe_tecnico']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$trabajos = $conn->query("
    SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap,
           u.nombre as tecnico_nombre, u.apellido_paterno as tecnico_ap,
           (SELECT GROUP_CONCAT(accion, ' - ', descripcion, ' [', fecha_registro, ']') 
            FROM seguimiento_trabajos WHERE equipo_id = e.id) as historial
    FROM equipos e
    JOIN clientes c ON e.cliente_id = c.id
    JOIN asignaciones_tecnico at ON e.id = at.equipo_id
    JOIN usuarios u ON at.tecnico_id = u.id
    WHERE e.sucursal_actual_id = $sucursal_id AND e.estado IN ('en_reparacion', 'completado')
    ORDER BY e.fecha_registro DESC
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'seguimiento'); ?>
        <div class="main-content">
            <?php renderTopbar('Seguimiento de Trabajos'); ?>
            <div class="content-area">
                <div class="card">
                    <div class="card-header"><h2>Trabajos en Curso y Completados</h2></div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr><th>Equipo</th><th>Cliente</th><th>Técnico</th><th>Estado</th><th>Historial</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trabajos as $t): ?>
                                <tr>
                                    <td><?= ucfirst($t['tipo_equipo']) ?> <?= sanitizar($t['marca']) ?></td>
                                    <td><?= sanitizar($t['cliente_nombre'] . ' ' . $t['cliente_ap']) ?></td>
                                    <td><?= sanitizar($t['tecnico_nombre'] . ' ' . $t['tecnico_ap']) ?></td>
                                    <td><span class="badge <?= $t['estado'] === 'en_reparacion' ? 'badge-rojo' : 'badge-verde' ?>"><?= ucfirst(str_replace('_', ' ', $t['estado'])) ?></span></td>
                                    <td><small><?= sanitizar($t['historial'] ?? 'Sin registros') ?></small></td>
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
