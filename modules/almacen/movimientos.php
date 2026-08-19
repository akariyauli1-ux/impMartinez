<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['almacenista']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repuesto_id = intval($_POST['repuesto_id']);
    $tipo = $_POST['tipo'];
    $cantidad = intval($_POST['cantidad']);
    $motivo = sanitizar($_POST['motivo']);
    $equipo_id = !empty($_POST['equipo_id']) ? intval($_POST['equipo_id']) : null;
    
    $repuesto = $conn->query("SELECT stock FROM repuestos WHERE id = $repuesto_id")->fetch_assoc();
    
    if ($tipo === 'salida' && $repuesto['stock'] < $cantidad) {
        $mensaje = 'Error: Stock insuficiente.';
    } else {
        $stmt = $conn->prepare("INSERT INTO movimientos_inventario (repuesto_id, tipo, cantidad, motivo, almacenista_id, equipo_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isisii", $repuesto_id, $tipo, $cantidad, $motivo, $usuario['id'], $equipo_id);
        $stmt->execute();
        $stmt->close();
        
        if ($tipo === 'entrada') {
            $conn->query("UPDATE repuestos SET stock = stock + $cantidad WHERE id = $repuesto_id");
        } else {
            $conn->query("UPDATE repuestos SET stock = stock - $cantidad WHERE id = $repuesto_id");
        }
        $mensaje = 'Movimiento registrado correctamente.';
    }
}

$repuestos = $conn->query("SELECT id, nombre, stock FROM repuestos WHERE sucursal_id = $sucursal_id ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$movimientos = $conn->query("
    SELECT m.*, r.nombre as repuesto_nombre, u.nombre as almacenista_nombre
    FROM movimientos_inventario m
    JOIN repuestos r ON m.repuesto_id = r.id
    JOIN usuarios u ON m.almacenista_id = u.id
    WHERE r.sucursal_id = $sucursal_id
    ORDER BY m.fecha_movimiento DESC LIMIT 50
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'movimientos'); ?>
        <div class="main-content">
            <?php renderTopbar('Movimientos de Inventario'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?><div class="alert <?= strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success' ?>"><?= $mensaje ?></div><?php endif; ?>
                
                <div class="card">
                    <div class="card-header"><h2>Registrar Movimiento</h2></div>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Repuesto *</label>
                                <select name="repuesto_id" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($repuestos as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= sanitizar($r['nombre']) ?> (Stock: <?= $r['stock'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tipo *</label>
                                <select name="tipo" required>
                                    <option value="entrada">Entrada</option>
                                    <option value="salida">Salida</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cantidad *</label>
                                <input type="number" name="cantidad" min="1" required>
                            </div>
                        </div>
                        <div class="form-group"><label>Motivo</label><input type="text" name="motivo" placeholder="Motivo del movimiento"></div>
                        <div class="form-group"><label>ID Equipo (si aplica)</label><input type="number" name="equipo_id" placeholder="Opcional"></div>
                        <button type="submit" class="btn btn-primary">Registrar Movimiento</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Últimos Movimientos</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Fecha</th><th>Repuesto</th><th>Tipo</th><th>Cantidad</th><th>Motivo</th><th>Almacenista</th></tr></thead>
                            <tbody>
                                <?php foreach ($movimientos as $m): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])) ?></td>
                                    <td><?= sanitizar($m['repuesto_nombre']) ?></td>
                                    <td><span class="badge <?= $m['tipo'] === 'entrada' ? 'badge-verde' : 'badge-rojo' ?>"><?= ucfirst($m['tipo']) ?></span></td>
                                    <td><?= $m['cantidad'] ?></td>
                                    <td><?= sanitizar($m['motivo'] ?? '-') ?></td>
                                    <td><?= sanitizar($m['almacenista_nombre']) ?></td>
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
