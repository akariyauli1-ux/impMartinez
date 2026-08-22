<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';

$usuario = verificarRol(['admin_sucursal']);

$conn = getConexion();
$sucursal_id = $usuario['sucursal_id'];
$mensaje = '';
$fecha = $_GET['fecha'] ?? date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_post = $_POST['fecha'];
    
    if (isset($_POST['empleados'])) {
        foreach ($_POST['empleados'] as $emp_id => $datos) {
            $hora_entrada = !empty($datos['hora_entrada']) ? $datos['hora_entrada'] : null;
            $hora_salida = !empty($datos['hora_salida']) ? $datos['hora_salida'] : null;
            $estado = $datos['estado'] ?? 'ausente';
            $observaciones = sanitizar($datos['observaciones'] ?? '');
            
            $stmt = $conn->prepare("INSERT INTO asistencia (usuario_id, fecha, hora_entrada, hora_salida, estado, observaciones, registrado_por) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE hora_entrada=VALUES(hora_entrada), hora_salida=VALUES(hora_salida), estado=VALUES(estado), observaciones=VALUES(observaciones), registrado_por=VALUES(registrado_por)");
            $stmt->bind_param("isssssi", $emp_id, $fecha_post, $hora_entrada, $hora_salida, $estado, $observaciones, $usuario['id']);
            $stmt->execute();
            $stmt->close();
        }
        $mensaje = 'Asistencia registrada correctamente';
    }
}

$personal = $conn->query("
    SELECT u.id, u.nombre, u.apellido_paterno, u.rol,
           a.hora_entrada, a.hora_salida, a.estado, a.observaciones
    FROM usuarios u
    LEFT JOIN asistencia a ON u.id = a.usuario_id AND a.fecha = '$fecha'
    WHERE u.sucursal_id = $sucursal_id AND u.activo = 1 
    AND u.rol IN ('tecnico', 'recepcionista', 'almacenista', 'jefe_tecnico')
    ORDER BY u.apellido_paterno
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
    <style>
        .check-cell {
            text-align: center;
        }
        .check-cell input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .hora-input {
            width: 100px;
            padding: 6px;
            font-size: 0.85rem;
        }
        .estado-label {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .estado-presente { background: #E8F5E9; color: #2E7D32; }
        .estado-tardanza { background: #FFF8E1; color: #F57F17; }
        .estado-ausente { background: #FFEBEE; color: #C62828; }
    </style>
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'asistencia'); ?>
        <div class="main-content">
            <?php renderTopbar('Registro de Asistencia'); ?>
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Fecha: <?= date('d/m/Y', strtotime($fecha)) ?></h2>
                        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                            <input type="date" name="fecha" value="<?= $fecha ?>" style="padding: 8px; border: 2px solid var(--gris-claro); border-radius: 6px;">
                            <button type="submit" class="btn btn-secondary btn-sm">Cambiar Fecha</button>
                        </form>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="fecha" value="<?= $fecha ?>">
                        <input type="hidden" name="accion" value="guardar_asistencia">
                        
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Empleado</th>
                                        <th>Cargo</th>
                                        <th>Entrada</th>
                                        <th>Hora Entrada</th>
                                        <th>Salida</th>
                                        <th>Hora Salida</th>
                                        <th>Obs.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($personal as $p): 
                                        $rol_labels = [
                                            'tecnico' => 'Técnico',
                                            'recepcionista' => 'Recepcionista',
                                            'almacenista' => 'Almacenista',
                                            'jefe_tecnico' => 'Jefe Técnico'
                                        ];
                                    ?>
                                    <tr>
                                        <td><strong><?= sanitizar($p['nombre'] . ' ' . $p['apellido_paterno']) ?></strong></td>
                                        <td><span class="badge badge-negro"><?= $rol_labels[$p['rol']] ?? $p['rol'] ?></span></td>
                                        <td class="check-cell">
                                            <input type="checkbox" name="empleados[<?= $p['id'] ?>][entrada_check]" 
                                                   <?= $p['hora_entrada'] ? 'checked' : '' ?>
                                                   onchange="toggleHora(this, 'entrada_<?= $p['id'] ?>')">
                                            <input type="hidden" name="empleados[<?= $p['id'] ?>][estado]" value="<?= $p['estado'] ?? 'ausente' ?>">
                                        </td>
                                        <td>
                                            <input type="time" name="empleados[<?= $p['id'] ?>][hora_entrada]" 
                                                   id="entrada_<?= $p['id'] ?>"
                                                   value="<?= $p['hora_entrada'] ?? '' ?>" 
                                                   class="hora-input"
                                                   <?= !$p['hora_entrada'] ? 'disabled' : '' ?>>
                                        </td>
                                        <td class="check-cell">
                                            <input type="checkbox" name="empleados[<?= $p['id'] ?>][salida_check]" 
                                                   <?= $p['hora_salida'] ? 'checked' : '' ?>
                                                   onchange="toggleHora(this, 'salida_<?= $p['id'] ?>')">
                                        </td>
                                        <td>
                                            <input type="time" name="empleados[<?= $p['id'] ?>][hora_salida]" 
                                                   id="salida_<?= $p['id'] ?>"
                                                   value="<?= $p['hora_salida'] ?? '' ?>" 
                                                   class="hora-input"
                                                   <?= !$p['hora_salida'] ? 'disabled' : '' ?>>
                                        </td>
                                        <td>
                                            <input type="text" name="empleados[<?= $p['id'] ?>][observaciones]" 
                                                   value="<?= sanitizar($p['observaciones'] ?? '') ?>" 
                                                   placeholder="Obs." style="width: 120px; padding: 6px; font-size: 0.85rem;">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($personal)): ?>
                                    <tr><td colspan="7" style="text-align:center; padding: 20px;">No hay personal en esta sucursal</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($personal)): ?>
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">Guardar Asistencia</button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function toggleHora(checkbox, inputId) {
            const input = document.getElementById(inputId);
            if (checkbox.checked) {
                input.disabled = false;
                if (!input.value) {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    input.value = hours + ':' + minutes;
                }
            } else {
                input.disabled = true;
                input.value = '';
            }
        }
    </script>
</body>
</html>
