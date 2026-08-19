<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['almacenista']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repuesto_id = intval($_POST['repuesto_id']);
    $cantidad = intval($_POST['cantidad']);
    
    $stmt = $conn->prepare("INSERT INTO pedidos_repuestos (sucursal_id, repuesto_id, cantidad, solicitado_por) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $sucursal_id, $repuesto_id, $cantidad, $usuario['id']);
    if ($stmt->execute()) {
        $mensaje = 'Pedido solicitado correctamente.';
    }
    $stmt->close();
}

$repuestos = $conn->query("SELECT id, nombre FROM repuestos WHERE sucursal_id = $sucursal_id ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$pedidos = $conn->query("
    SELECT p.*, r.nombre as repuesto_nombre, u.nombre as solicitante
    FROM pedidos_repuestos p
    JOIN repuestos r ON p.repuesto_id = r.id
    JOIN usuarios u ON p.solicitado_por = u.id
    WHERE p.sucursal_id = $sucursal_id
    ORDER BY p.fecha_solicitud DESC
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'pedidos'); ?>
        <div class="main-content">
            <?php renderTopbar('Pedidos de Repuestos'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
                
                <div class="card">
                    <div class="card-header"><h2>Solicitar Pedido</h2></div>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Repuesto *</label>
                                <select name="repuesto_id" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($repuestos as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= sanitizar($r['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cantidad *</label>
                                <input type="number" name="cantidad" min="1" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Solicitar Pedido</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Pedidos Realizados</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Fecha</th><th>Repuesto</th><th>Cantidad</th><th>Estado</th><th>Solicitante</th></tr></thead>
                            <tbody>
                                <?php foreach ($pedidos as $p): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_solicitud'])) ?></td>
                                    <td><?= sanitizar($p['repuesto_nombre']) ?></td>
                                    <td><?= $p['cantidad'] ?></td>
                                    <td><span class="badge badge-negro"><?= ucfirst($p['estado']) ?></span></td>
                                    <td><?= sanitizar($p['solicitante']) ?></td>
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
