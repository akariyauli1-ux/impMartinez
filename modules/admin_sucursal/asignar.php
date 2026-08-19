<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['admin_sucursal']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipo_id = intval($_POST['equipo_id']);
    $sucursal_destino = intval($_POST['sucursal_destino']);
    
    if ($sucursal_destino != $sucursal_id) {
        $stmt = $conn->prepare("UPDATE equipos SET sucursal_actual_id = ?, estado = 'asignado_sucursal' WHERE id = ?");
        $stmt->bind_param("ii", $sucursal_destino, $equipo_id);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO asignaciones_sucursal (equipo_id, sucursal_origen_id, sucursal_destino_id, admin_origen_id, motivo) VALUES (?, ?, ?, ?, ?)");
        $motivo = sanitizar($_POST['motivo'] ?? '');
        $stmt->bind_param("iiiii", $equipo_id, $sucursal_id, $sucursal_destino, $usuario['id'], $motivo);
        $stmt->execute();
        $stmt->close();
        
        $mensaje = 'Equipo asignado correctamente a la sucursal destino.';
    } else {
        $conn->query("UPDATE equipos SET estado = 'asignado_sucursal' WHERE id = $equipo_id");
        $mensaje = 'Equipo marcado para asignación a técnico de esta sucursal.';
    }
}

$equipos_pendientes = $conn->query("
    SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap
    FROM equipos e
    JOIN clientes c ON e.cliente_id = c.id
    WHERE e.sucursal_actual_id = $sucursal_id AND e.estado = 'pendiente_asignacion'
    ORDER BY e.fecha_registro ASC
")->fetch_all(MYSQLI_ASSOC);

$sucursales = $conn->query("SELECT id, nombre FROM sucursales WHERE activo = 1 AND id != $sucursal_id")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar a Sucursal - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'asignar'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Asignar Equipos a Sucursal'); ?>
            
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Equipos Pendientes de Asignación</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Equipo</th>
                                    <th>Falla</th>
                                    <th>Asignar a</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipos_pendientes as $eq): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="equipo_id" value="<?= $eq['id'] ?>">
                                        <td><?= date('d/m/Y', strtotime($eq['fecha_registro'])) ?></td>
                                        <td><?= sanitizar($eq['cliente_nombre'] . ' ' . $eq['cliente_ap']) ?></td>
                                        <td><?= ucfirst($eq['tipo_equipo']) ?> <?= sanitizar($eq['marca']) ?> <?= sanitizar($eq['modelo']) ?></td>
                                        <td><?= sanitizar(substr($eq['descripcion_falla'], 0, 50)) ?></td>
                                        <td>
                                            <select name="sucursal_destino" required style="padding: 6px; margin-bottom: 5px;">
                                                <option value="<?= $sucursal_id ?>">Esta sucursal (<?= sanitizar($usuario['sucursal_nombre']) ?>)</option>
                                                <?php foreach ($sucursales as $s): ?>
                                                    <option value="<?= $s['id'] ?>"><?= sanitizar($s['nombre']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="text" name="motivo" placeholder="Motivo (opcional)" style="padding: 6px; margin-bottom: 5px; width: 100%;">
                                            <button type="submit" class="btn btn-primary btn-sm">Asignar</button>
                                        </td>
                                    </form>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($equipos_pendientes)): ?>
                                <tr><td colspan="5" style="text-align:center; padding: 20px;">No hay equipos pendientes</td></tr>
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
