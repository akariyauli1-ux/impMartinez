<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['admin_sucursal']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$pendientes = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE sucursal_actual_id = $sucursal_id AND estado = 'asignado_sucursal'")->fetch_assoc()['total'];
$en_reparacion = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE sucursal_actual_id = $sucursal_id AND estado = 'en_reparacion'")->fetch_assoc()['total'];
$completados = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE sucursal_actual_id = $sucursal_id AND estado IN ('completado', 'entregado')")->fetch_assoc()['total'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Sucursal - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'dashboard'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Dashboard Administrador de Sucursal'); ?>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $pendientes ?></div>
                        <div class="stat-label">Equipos Pendientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $en_reparacion ?></div>
                        <div class="stat-label">En Reparación</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $completados ?></div>
                        <div class="stat-label">Completados</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Acciones</h2>
                    </div>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="/impMartines/modules/admin_sucursal/pendientes.php" class="btn btn-primary">Ver Pendientes</a>
                        <a href="/impMartines/modules/admin_sucursal/asignar.php" class="btn btn-secondary">Asignar a Sucursal</a>
                        <a href="/impMartines/modules/admin_sucursal/reportes.php" class="btn btn-outline">Reportes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
