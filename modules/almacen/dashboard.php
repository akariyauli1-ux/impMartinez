<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['almacenista']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$total_repuestos = $conn->query("SELECT COUNT(*) as total FROM repuestos WHERE sucursal_id = $sucursal_id")->fetch_assoc()['total'];
$stock_bajo = $conn->query("SELECT COUNT(*) as total FROM repuestos WHERE sucursal_id = $sucursal_id AND stock <= stock_minimo")->fetch_assoc()['total'];
$pedidos_pendientes = $conn->query("SELECT COUNT(*) as total FROM pedidos_repuestos WHERE sucursal_id = $sucursal_id AND estado = 'solicitado'")->fetch_assoc()['total'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Almacén - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'dashboard'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Dashboard Almacén'); ?>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $total_repuestos ?></div>
                        <div class="stat-label">Total Repuestos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $stock_bajo ?></div>
                        <div class="stat-label">Stock Bajo</div>
                    </div>
                    <div class="stat-card negro">
                        <div class="stat-value"><?= $pedidos_pendientes ?></div>
                        <div class="stat-label">Pedidos Pendientes</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Acciones</h2>
                    </div>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="/impMartines/modules/almacen/inventario.php" class="btn btn-primary">Ver Inventario</a>
                        <a href="/impMartines/modules/almacen/movimientos.php" class="btn btn-secondary">Movimientos</a>
                        <a href="/impMartines/modules/almacen/pedidos.php" class="btn btn-outline">Pedidos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
