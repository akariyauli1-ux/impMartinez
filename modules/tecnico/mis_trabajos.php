<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['tecnico']);

$conn = getConexion();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $equipo_id = intval($_POST['equipo_id']);
    $descripcion = sanitizar($_POST['descripcion'] ?? '');
    
    $stmt = $conn->prepare("INSERT INTO seguimiento_trabajos (equipo_id, tecnico_id, accion, descripcion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $equipo_id, $usuario['id'], $accion, $descripcion);
    $stmt->execute();
    $stmt->close();
    
    if ($accion === 'completado') {
        $conn->query("UPDATE equipos SET estado = 'completado' WHERE id = $equipo_id");
    }
    
    $mensaje = 'Registro guardado correctamente.';
}

$mis_trabajos = $conn->query("
    SELECT e.*, c.nombre as cliente_nombre, c.apellido_paterno as cliente_ap, c.telefono as cliente_tel,
           at.fecha_asignacion,
           (SELECT GROUP_CONCAT(accion, '|', descripcion, '|', fecha_registro SEPARATOR ';;') 
            FROM seguimiento_trabajos WHERE equipo_id = e.id AND tecnico_id = " . $usuario['id'] . ") as historial
    FROM equipos e
    JOIN clientes c ON e.cliente_id = c.id
    JOIN asignaciones_tecnico at ON e.id = at.equipo_id
    WHERE at.tecnico_id = " . $usuario['id'] . " AND e.estado NOT IN ('entregado')
    ORDER BY e.fecha_registro DESC
")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Trabajos - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'mis_trabajos'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Mis Trabajos Asignados'); ?>
            
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <?php foreach ($mis_trabajos as $trabajo): 
                    $fotos = json_decode($trabajo['fotos'] ?? '[]', true);
                    $historial_items = [];
                    if ($trabajo['historial']) {
                        $items = explode(';;', $trabajo['historial']);
                        foreach ($items as $item) {
                            $parts = explode('|', $item);
                            if (count($parts) >= 3) {
                                $historial_items[] = ['accion' => $parts[0], 'descripcion' => $parts[1], 'fecha' => $parts[2]];
                            }
                        }
                    }
                ?>
                <div class="card">
                    <div class="card-header">
                        <h2><?= ucfirst($trabajo['tipo_equipo']) ?> <?= sanitizar($trabajo['marca']) ?> <?= sanitizar($trabajo['modelo']) ?></h2>
                        <span class="badge <?= $trabajo['estado'] === 'en_reparacion' ? 'badge-rojo' : 'badge-verde' ?>">
                            <?= ucfirst(str_replace('_', ' ', $trabajo['estado'])) ?>
                        </span>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <p><strong>Cliente:</strong> <?= sanitizar($trabajo['cliente_nombre'] . ' ' . $trabajo['cliente_ap']) ?> - <?= sanitizar($trabajo['cliente_tel']) ?></p>
                        <p><strong>Falla reportada:</strong> <?= sanitizar($trabajo['descripcion_falla']) ?></p>
                        <p><strong>Asignado:</strong> <?= date('d/m/Y H:i', strtotime($trabajo['fecha_asignacion'])) ?></p>
                        
                        <?php if (!empty($fotos)): ?>
                        <div class="foto-grid">
                            <?php foreach ($fotos as $foto): ?>
                                <div class="foto-preview"><img src="/impMartines/uploads/fotos_equipos/<?= $foto ?>" alt="Foto equipo"></div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($historial_items)): ?>
                    <div style="margin-bottom: 15px;">
                        <strong>Historial:</strong>
                        <div class="timeline" style="margin-top: 10px;">
                            <?php foreach ($historial_items as $h): ?>
                            <div class="timeline-item">
                                <div class="time"><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></div>
                                <div class="content"><strong><?= ucfirst(str_replace('_', ' ', $h['accion'])) ?>:</strong> <?= sanitizar($h['descripcion']) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($trabajo['estado'] !== 'completado'): ?>
                    <form method="POST" style="border-top: 1px solid #E0E0E0; padding-top: 15px;">
                        <input type="hidden" name="equipo_id" value="<?= $trabajo['id'] ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Acción</label>
                                <select name="accion" required>
                                    <option value="recibido">Reportar recibido</option>
                                    <option value="inicio_reparacion">Iniciar reparación</option>
                                    <option value="nota_tecnica">Agregar nota técnica</option>
                                    <option value="completado">Marcar como completado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Descripción</label>
                                <input type="text" name="descripcion" placeholder="Detalle de la acción">
                            </div>
                            <div class="form-group" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="btn btn-primary">Registrar</button>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($mis_trabajos)): ?>
                <div class="card">
                    <p style="text-align: center; padding: 20px;">No tienes trabajos asignados actualmente.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
