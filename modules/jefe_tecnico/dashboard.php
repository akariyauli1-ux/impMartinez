<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['jefe_tecnico']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$tecnicos = $conn->query("SELECT u.id, u.nombre, u.apellido_paterno, COUNT(at.id) as trabajos FROM usuarios u LEFT JOIN asignaciones_tecnico at ON u.id = at.tecnico_id LEFT JOIN equipos e ON at.equipo_id = e.id AND e.estado NOT IN ('completado', 'entregado') WHERE u.rol = 'tecnico' AND u.sucursal_id = $sucursal_id AND u.activo = 1 GROUP BY u.id")->fetch_all(MYSQLI_ASSOC);
$pendientes_asignar = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE sucursal_actual_id = $sucursal_id AND estado = 'asignado_sucursal' AND id NOT IN (SELECT equipo_id FROM asignaciones_tecnico)")->fetch_assoc()['total'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Jefe Técnico - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'dashboard'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Dashboard Jefe Técnico'); ?>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $pendientes_asignar ?></div>
                        <div class="stat-label">Pendientes de Asignar</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= count($tecnicos) ?></div>
                        <div class="stat-label">Técnicos Disponibles</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Carga de Trabajo por Técnico</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Técnico</th>
                                    <th>Trabajos Actuales</th>
                                    <th>Disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tecnicos as $t): ?>
                                <tr>
                                    <td><?= sanitizar($t['nombre'] . ' ' . $t['apellido_paterno']) ?></td>
                                    <td><?= $t['trabajos'] ?>/4</td>
                                    <td>
                                        <?php if ($t['trabajos'] < 4): ?>
                                            <span class="badge badge-verde">Sí</span>
                                        <?php else: ?>
                                            <span class="badge badge-rojo">No</span>
                                        <?php endif; ?>
                                    </td>
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
