<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['tecnico']);

$conn = getConexion();
$mis_trabajos = $conn->query("SELECT COUNT(*) as total FROM asignaciones_tecnico at JOIN equipos e ON at.equipo_id = e.id WHERE at.tecnico_id = " . $usuario['id'] . " AND e.estado NOT IN ('completado', 'entregado')")->fetch_assoc()['total'];
$completados = $conn->query("SELECT COUNT(*) as total FROM asignaciones_tecnico at JOIN equipos e ON at.equipo_id = e.id WHERE at.tecnico_id = " . $usuario['id'] . " AND e.estado IN ('completado', 'entregado')")->fetch_assoc()['total'];
$disponibles = 4 - $mis_trabajos;
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Técnico - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'dashboard'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Dashboard Técnico'); ?>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $mis_trabajos ?>/4</div>
                        <div class="stat-label">Trabajos Asignados</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $disponibles ?></div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $completados ?></div>
                        <div class="stat-label">Completados</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Mis Trabajos</h2>
                    </div>
                    <a href="/impMartines/modules/tecnico/mis_trabajos.php" class="btn btn-primary">Ver Mis Trabajos</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
