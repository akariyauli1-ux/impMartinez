<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['admin_sucursal']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$reportes = $conn->query("
    SELECT e.estado, COUNT(*) as cantidad
    FROM equipos e
    WHERE e.sucursal_actual_id = $sucursal_id
    GROUP BY e.estado
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'reportes'); ?>
        <div class="main-content">
            <?php renderTopbar('Reportes de Sucursal'); ?>
            <div class="content-area">
                <div class="card">
                    <div class="card-header"><h2>Estado de Equipos en Sucursal</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Estado</th><th>Cantidad</th></tr></thead>
                            <tbody>
                                <?php foreach ($reportes as $r): ?>
                                <tr><td><?= ucfirst(str_replace('_', ' ', $r['estado'])) ?></td><td><?= $r['cantidad'] ?></td></tr>
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
