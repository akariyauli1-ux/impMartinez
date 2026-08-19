<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['gerente']);

$conn = getConexion();

$total_equipos = $conn->query("SELECT COUNT(*) as total FROM equipos")->fetch_assoc()['total'];
$en_reparacion = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE estado = 'en_reparacion'")->fetch_assoc()['total'];
$completados = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE estado IN ('completado', 'entregado')")->fetch_assoc()['total'];
$pendientes = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE estado IN ('registrado', 'pendiente_asignacion')")->fetch_assoc()['total'];
$total_sucursales = $conn->query("SELECT COUNT(*) as total FROM sucursales WHERE activo = 1")->fetch_assoc()['total'];
$total_tecnicos = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'tecnico' AND activo = 1")->fetch_assoc()['total'];

$sucursales_data = $conn->query("
    SELECT s.nombre, 
           COUNT(e.id) as equipos,
           SUM(CASE WHEN e.estado = 'en_reparacion' THEN 1 ELSE 0 END) as en_reparacion,
           SUM(CASE WHEN e.estado IN ('completado', 'entregado') THEN 1 ELSE 0 END) as completados
    FROM sucursales s 
    LEFT JOIN equipos e ON e.sucursal_actual_id = s.id
    WHERE s.activo = 1
    GROUP BY s.id
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gerente - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'dashboard'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Dashboard General'); ?>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $total_equipos ?></div>
                        <div class="stat-label">Total Equipos Registrados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $en_reparacion ?></div>
                        <div class="stat-label">En Reparación</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $completados ?></div>
                        <div class="stat-label">Completados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $pendientes ?></div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $total_sucursales ?></div>
                        <div class="stat-label">Sucursales Activas</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $total_tecnicos ?></div>
                        <div class="stat-label">Técnicos</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Resumen por Sucursal</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Sucursal</th>
                                    <th>Equipos</th>
                                    <th>En Reparación</th>
                                    <th>Completados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sucursales_data as $s): ?>
                                <tr>
                                    <td><strong><?= sanitizar($s['nombre']) ?></strong></td>
                                    <td><?= $s['equipos'] ?></td>
                                    <td><span class="badge badge-amarillo"><?= $s['en_reparacion'] ?></span></td>
                                    <td><span class="badge badge-verde"><?= $s['completados'] ?></span></td>
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
