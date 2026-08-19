<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['almacenista']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre']);
    $descripcion = sanitizar($_POST['descripcion']);
    $categoria = sanitizar($_POST['categoria']);
    $stock = intval($_POST['stock']);
    $stock_minimo = intval($_POST['stock_minimo']);
    $precio = floatval($_POST['precio_unitario']);
    
    $stmt = $conn->prepare("INSERT INTO repuestos (nombre, descripcion, categoria, stock, stock_minimo, precio_unitario, sucursal_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssidiI", $nombre, $descripcion, $categoria, $stock, $stock_minimo, $precio, $sucursal_id);
    if ($stmt->execute()) {
        $mensaje = 'Repuesto agregado al inventario.';
    }
    $stmt->close();
}

$inventario = $conn->query("SELECT * FROM repuestos WHERE sucursal_id = $sucursal_id ORDER BY categoria, nombre")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'inventario'); ?>
        <div class="main-content">
            <?php renderTopbar('Inventario de Repuestos'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
                
                <div class="card">
                    <div class="card-header"><h2>Agregar Repuesto</h2></div>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" required></div>
                            <div class="form-group"><label>Categoría</label><input type="text" name="categoria" placeholder="Pantallas, baterías, etc."></div>
                            <div class="form-group"><label>Stock Inicial</label><input type="number" name="stock" value="0" min="0"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Stock Mínimo</label><input type="number" name="stock_minimo" value="5" min="0"></div>
                            <div class="form-group"><label>Precio Unitario</label><input type="number" name="precio_unitario" step="0.01" min="0"></div>
                        </div>
                        <div class="form-group"><label>Descripción</label><textarea name="descripcion" rows="2"></textarea></div>
                        <button type="submit" class="btn btn-primary">Agregar al Inventario</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header"><h2>Inventario Actual</h2></div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Nombre</th><th>Categoría</th><th>Stock</th><th>Mínimo</th><th>Precio</th><th>Estado</th></tr></thead>
                            <tbody>
                                <?php foreach ($inventario as $r): ?>
                                <tr>
                                    <td><?= sanitizar($r['nombre']) ?></td>
                                    <td><?= sanitizar($r['categoria'] ?? '-') ?></td>
                                    <td><strong><?= $r['stock'] ?></strong></td>
                                    <td><?= $r['stock_minimo'] ?></td>
                                    <td>S/ <?= number_format($r['precio_unitario'], 2) ?></td>
                                    <td>
                                        <?php if ($r['stock'] <= $r['stock_minimo']): ?>
                                            <span class="badge badge-rojo">Stock Bajo</span>
                                        <?php else: ?>
                                            <span class="badge badge-verde">OK</span>
                                        <?php endif; ?>
                                    </td>
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
