<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['recepcionista']);

$conn = getConexion();
$mis_registros = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE recepcionista_id = " . $usuario['id'])->fetch_assoc()['total'];
$pendientes = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE recepcionista_id = " . $usuario['id'] . " AND estado IN ('registrado', 'pendiente_asignacion')")->fetch_assoc()['total'];
$hoy = $conn->query("SELECT COUNT(*) as total FROM equipos WHERE recepcionista_id = " . $usuario['id'] . " AND DATE(fecha_registro) = CURDATE()")->fetch_assoc()['total'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Recepción - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'dashboard'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Dashboard Recepción'); ?>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $hoy ?></div>
                        <div class="stat-label">Registros de Hoy</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $mis_registros ?></div>
                        <div class="stat-label">Total Registrados</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $pendientes ?></div>
                        <div class="stat-label">Pendientes de Asignar</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Acciones Rápidas</h2>
                    </div>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="/impMartines/modules/recepcion/nuevo_cliente.php" class="btn btn-primary">Nuevo Cliente</a>
                        <a href="/impMartines/modules/recepcion/nuevo_equipo.php" class="btn btn-secondary">Registrar Equipo</a>
                        <a href="/impMartines/modules/recepcion/mis_registros.php" class="btn btn-outline">Ver Mis Registros</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
