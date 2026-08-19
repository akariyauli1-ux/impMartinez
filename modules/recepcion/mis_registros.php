<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['recepcionista']);

$conn = getConexion();
$registros = $conn->query("
    SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel,
           s.nombre as sucursal_nombre
    FROM equipos e
    JOIN clientes c ON e.cliente_id = c.id
    LEFT JOIN sucursales s ON e.sucursal_actual_id = s.id
    WHERE e.recepcionista_id = " . $usuario['id'] . "
    ORDER BY e.fecha_registro DESC
")->fetch_all(MYSQLI_ASSOC);
$conn->close();

$estado_labels = [
    'registrado' => ['Registrado', 'badge-gris'],
    'pendiente_asignacion' => ['Pendiente Asignación', 'badge-amarillo'],
    'asignado_sucursal' => ['Asignado a Sucursal', 'badge-negro'],
    'en_reparacion' => ['En Reparación', 'badge-rojo'],
    'completado' => ['Completado', 'badge-verde'],
    'entregado' => ['Entregado', 'badge-verde']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Registros - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'mis_registros'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Mis Registros de Equipos'); ?>
            
            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h2>Equipos Registrados</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Equipo</th>
                                    <th>Falla</th>
                                    <th>Sucursal Actual</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registros as $r): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r['fecha_registro'])) ?></td>
                                    <td><?= sanitizar($r['cliente_nombre'] . ' ' . $r['cliente_ap']) ?><br><small><?= sanitizar($r['cliente_tel']) ?></small></td>
                                    <td><?= ucfirst($r['tipo_equipo']) ?> <?= sanitizar($r['marca']) ?> <?= sanitizar($r['modelo']) ?></td>
                                    <td><?= sanitizar(substr($r['descripcion_falla'], 0, 50)) ?>...</td>
                                    <td><?= sanitizar($r['sucursal_nombre'] ?? 'Sin asignar') ?></td>
                                    <td><span class="badge <?= $estado_labels[$r['estado']][1] ?>"><?= $estado_labels[$r['estado']][0] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($registros)): ?>
                                <tr><td colspan="6" style="text-align:center; padding: 20px;">No hay registros aún</td></tr>
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
