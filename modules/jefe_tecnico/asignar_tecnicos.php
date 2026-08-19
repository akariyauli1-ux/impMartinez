<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['jefe_tecnico']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipo_id = intval($_POST['equipo_id']);
    $tecnico_id = intval($_POST['tecnico_id']);
    
    $cant = $conn->query("SELECT COUNT(*) as total FROM asignaciones_tecnico at JOIN equipos e ON at.equipo_id = e.id WHERE at.tecnico_id = $tecnico_id AND e.estado NOT IN ('completado', 'entregado')")->fetch_assoc()['total'];
    
    if ($cant >= 4) {
        $mensaje = 'Error: El técnico ya tiene 4 trabajos asignados (máximo permitido).';
    } else {
        $stmt = $conn->prepare("INSERT INTO asignaciones_tecnico (equipo_id, tecnico_id, jefe_tecnico_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $equipo_id, $tecnico_id, $usuario['id']);
        $stmt->execute();
        $stmt->close();
        
        $conn->query("UPDATE equipos SET estado = 'en_reparacion' WHERE id = $equipo_id");
        $mensaje = 'Trabajo asignado correctamente al técnico.';
    }
}

$equipos_pendientes = $conn->query("
    SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap
    FROM equipos e
    JOIN clientes c ON e.cliente_id = c.id
    WHERE e.sucursal_actual_id = $sucursal_id AND e.estado = 'asignado_sucursal'
    AND e.id NOT IN (SELECT equipo_id FROM asignaciones_tecnico)
    ORDER BY e.fecha_registro ASC
")->fetch_all(MYSQLI_ASSOC);

$tecnicos = $conn->query("
    SELECT u.id, u.nombre, u.apellido_paterno,
           COUNT(CASE WHEN e.estado NOT IN ('completado', 'entregado') THEN 1 END) as trabajos
    FROM usuarios u
    LEFT JOIN asignaciones_tecnico at ON u.id = at.tecnico_id
    LEFT JOIN equipos e ON at.equipo_id = e.id
    WHERE u.rol = 'tecnico' AND u.sucursal_id = $sucursal_id AND u.activo = 1
    GROUP BY u.id
    ORDER BY u.apellido_paterno
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar a Técnicos - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'asignar'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Asignar Trabajos a Técnicos'); ?>
            
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert <?= strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success' ?>"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Carga de Trabajo - Técnicos</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr><th>Técnico</th><th>Trabajos</th><th>Estado</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tecnicos as $t): ?>
                                <tr>
                                    <td><?= sanitizar($t['nombre'] . ' ' . $t['apellido_paterno']) ?></td>
                                    <td><?= $t['trabajos'] ?>/4</td>
                                    <td>
                                        <?php if ($t['trabajos'] < 4): ?>
                                            <span class="badge badge-verde">Disponible</span>
                                        <?php else: ?>
                                            <span class="badge badge-rojo">Lleno</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Asignar Equipos Pendientes</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr><th>Fecha</th><th>Cliente</th><th>Equipo</th><th>Falla</th><th>Asignar a</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipos_pendientes as $eq): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="equipo_id" value="<?= $eq['id'] ?>">
                                        <td><?= date('d/m/Y', strtotime($eq['fecha_registro'])) ?></td>
                                        <td><?= sanitizar($eq['cliente_nombre'] . ' ' . $eq['cliente_ap']) ?></td>
                                        <td><?= ucfirst($eq['tipo_equipo']) ?> <?= sanitizar($eq['marca']) ?> <?= sanitizar($eq['modelo']) ?></td>
                                        <td><?= sanitizar(substr($eq['descripcion_falla'], 0, 40)) ?></td>
                                        <td>
                                            <select name="tecnico_id" required style="padding: 6px; margin-bottom: 5px;">
                                                <option value="">Seleccione técnico</option>
                                                <?php foreach ($tecnicos as $t): ?>
                                                    <?php if ($t['trabajos'] < 4): ?>
                                                        <option value="<?= $t['id'] ?>"><?= sanitizar($t['nombre'] . ' ' . $t['apellido_paterno']) ?> (<?= $t['trabajos'] ?>/4)</option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">Asignar</button>
                                        </td>
                                    </form>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($equipos_pendientes)): ?>
                                <tr><td colspan="5" style="text-align:center; padding: 20px;">No hay equipos pendientes de asignar</td></tr>
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
