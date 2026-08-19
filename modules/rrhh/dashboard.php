<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['rrhh']);

$conn = getConexion();
$total_personal = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1")->fetch_assoc()['total'];
$presentes_hoy = $conn->query("SELECT COUNT(*) as total FROM asistencia WHERE fecha = CURDATE() AND estado = 'presente'")->fetch_assoc()['total'];
$tardanzas_hoy = $conn->query("SELECT COUNT(*) as total FROM asistencia WHERE fecha = CURDATE() AND estado = 'tardanza'")->fetch_assoc()['total'];
$ausentes_hoy = $conn->query("SELECT COUNT(*) as total FROM asistencia WHERE fecha = CURDATE() AND estado = 'ausente'")->fetch_assoc()['total'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard RRHH - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'dashboard'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Dashboard Recursos Humanos'); ?>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $total_personal ?></div>
                        <div class="stat-label">Total Personal</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $presentes_hoy ?></div>
                        <div class="stat-label">Presentes Hoy</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $tardanzas_hoy ?></div>
                        <div class="stat-label">Tardanzas Hoy</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $ausentes_hoy ?></div>
                        <div class="stat-label">Ausentes Hoy</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Acciones</h2>
                    </div>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="/impMartines/modules/rrhh/asistencia.php" class="btn btn-primary">Registrar Asistencia</a>
                        <a href="/impMartines/modules/rrhh/inspecciones.php" class="btn btn-secondary">Inspecciones</a>
                        <a href="/impMartines/modules/rrhh/productividad.php" class="btn btn-outline">Productividad</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
