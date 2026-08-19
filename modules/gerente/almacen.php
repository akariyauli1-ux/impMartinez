<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['gerente']);

$conn = getConexion();
$estado_almacen = $conn->query("
    SELECT s.nombre as sucursal, 
           COUNT(r.id) as total_repuestos,
           SUM(r.stock) as stock_total,
           SUM(CASE WHEN r.stock <= r.stock_minimo THEN 1 ELSE 0 END) as stock_bajo,
           (SELECT COUNT(*) FROM pedidos_repuestos p WHERE p.sucursal_id = s.id AND p.estado = 'solicitado') as pedidos_pendientes
    FROM sucursales s
    LEFT JOIN repuestos r ON r.sucursal_id = s.id
    WHERE s.activo = 1
    GROUP BY s.id
")->fetch_all(MYSQLI_ASSOC);

$mas_pedidos = $conn->query("
    SELECT r.nombre, SUM(p.cantidad) as total_pedidos
    FROM pedidos_repuestos p
    JOIN repuestos r ON p.repuesto_id = r.id
    GROUP BY r.id
    ORDER BY total_pedidos DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado Almacén - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'almacen'); ?>
        <div class="main-content">
            <?php renderTopbar('Estado de Almacén por Sucursal'); ?>
            <div class="content-area">
                <div class="card">
                    <div class="card-header"><h2>Resumen de Almacén</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Sucursal</th><th>Repuestos</th><th>Stock Total</th><th>Stock Bajo</th><th>Pedidos Pendientes</th></tr></thead>
                            <tbody>
                                <?php foreach ($estado_almacen as $a): ?>
                                <tr>
                                    <td><strong><?= sanitizar($a['sucursal']) ?></strong></td>
                                    <td><?= $a['total_repuestos'] ?></td>
                                    <td><?= $a['stock_total'] ?></td>
                                    <td><span class="badge <?= $a['stock_bajo'] > 0 ? 'badge-rojo' : 'badge-verde' ?>"><?= $a['stock_bajo'] ?></span></td>
                                    <td><span class="badge badge-amarillo"><?= $a['pedidos_pendientes'] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Repuestos Más Solicitados</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>#</th><th>Repuesto</th><th>Total Pedidos</th></tr></thead>
                            <tbody>
                                <?php foreach ($mas_pedidos as $i => $p): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= sanitizar($p['nombre']) ?></td>
                                    <td><strong><?= $p['total_pedidos'] ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($mas_pedidos)): ?>
                                <tr><td colspan="3" style="text-align:center; padding: 20px;">No hay pedidos registrados</td></tr>
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
