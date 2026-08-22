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
    
    if (isset($_POST['inspecciones'])) {
        foreach ($_POST['inspecciones'] as $emp_id => $datos) {
            $limpieza_check = isset($datos['limpieza_check']) ? 'aprobado' : 'rechazado';
            $hora_limpieza = !empty($datos['hora_limpieza']) ? $datos['hora_limpieza'] : null;
            $obs_limpieza = sanitizar($datos['obs_limpieza'] ?? '');
            
            $uniforme_check = isset($datos['uniforme_check']) ? 'completo' : 'incompleto';
            $hora_uniforme = !empty($datos['hora_uniforme']) ? $datos['hora_uniforme'] : null;
            $obs_uniforme = sanitizar($datos['obs_uniforme'] ?? '');
            
            $observaciones = sanitizar($datos['observaciones'] ?? '');
            
            $stmt = $conn->prepare("INSERT INTO inspecciones (usuario_id, fecha, limpieza, uniforme, observaciones, registrado_por, hora_revision_limpieza, hora_revision_uniforme, obs_limpieza, obs_uniforme) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE limpieza=VALUES(limpieza), uniforme=VALUES(uniforme), observaciones=VALUES(observaciones), hora_revision_limpieza=VALUES(hora_revision_limpieza), hora_revision_uniforme=VALUES(hora_revision_uniforme), obs_limpieza=VALUES(obs_limpieza), obs_uniforme=VALUES(obs_uniforme)");
            $stmt->bind_param("isssisssss", $emp_id, $fecha_post, $limpieza_check, $uniforme_check, $observaciones, $usuario['id'], $hora_limpieza, $hora_uniforme, $obs_limpieza, $obs_uniforme);
            $stmt->execute();
            $stmt->close();
        }
        $mensaje = 'Inspección registrada correctamente';
    }
}

$personal = $conn->query("
    SELECT u.id, u.nombre, u.apellido_paterno, u.rol,
           i.limpieza, i.uniforme, i.hora_revision_limpieza, i.hora_revision_uniforme, 
           i.observaciones, i.obs_limpieza, i.obs_uniforme
    FROM usuarios u
    LEFT JOIN inspecciones i ON u.id = i.usuario_id AND i.fecha = '$fecha'
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
    <title>Limpieza y Uniforme - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
    <style>
        .check-cell {
            text-align: center;
        }
        .check-cell input[type="checkbox"] {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: var(--rojo);
        }
        .hora-input {
            width: 90px;
            padding: 5px;
            font-size: 0.8rem;
        }
        .obs-input {
            width: 100%;
            padding: 5px;
            font-size: 0.8rem;
            border: 1px solid var(--gris-claro);
            border-radius: 4px;
        }
        .section-header {
            background: var(--negro);
            color: var(--blanco);
            padding: 8px;
            text-align: center;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .badge-aprobado { background: #E8F5E9; color: #2E7D32; }
        .badge-rechazado { background: #FFEBEE; color: #C62828; }
        .badge-completo { background: #E8F5E9; color: #2E7D32; }
        .badge-incompleto { background: #FFEBEE; color: #C62828; }
    </style>
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'inspecciones'); ?>
        <div class="main-content">
            <?php renderTopbar('Inspección de Limpieza y Uniforme'); ?>
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
                        
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th rowspan="2">Empleado</th>
                                        <th rowspan="2">Cargo</th>
                                        <th colspan="3" class="section-header">LIMPIEZA DEL LOCAL</th>
                                        <th colspan="3" class="section-header">UNIFORME</th>
                                    </tr>
                                    <tr>
                                        <th>Check</th>
                                        <th>Hora</th>
                                        <th>Obs.</th>
                                        <th>Check</th>
                                        <th>Hora</th>
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
                                            <input type="checkbox" name="inspecciones[<?= $p['id'] ?>][limpieza_check]" 
                                                   <?= $p['limpieza'] === 'aprobado' ? 'checked' : '' ?>
                                                   onchange="toggleHora(this, 'limpieza_<?= $p['id'] ?>')">
                                        </td>
                                        <td>
                                            <input type="time" name="inspecciones[<?= $p['id'] ?>][hora_limpieza]" 
                                                   id="limpieza_<?= $p['id'] ?>"
                                                   value="<?= $p['hora_revision_limpieza'] ?? '' ?>" 
                                                   class="hora-input"
                                                   <?= $p['limpieza'] !== 'aprobado' ? 'disabled' : '' ?>>
                                        </td>
                                        <td>
                                            <input type="text" name="inspecciones[<?= $p['id'] ?>][obs_limpieza]" 
                                                   value="<?= sanitizar($p['obs_limpieza'] ?? '') ?>" 
                                                   class="obs-input" placeholder="Obs.">
                                        </td>
                                        
                                        <td class="check-cell">
                                            <input type="checkbox" name="inspecciones[<?= $p['id'] ?>][uniforme_check]" 
                                                   <?= $p['uniforme'] === 'completo' ? 'checked' : '' ?>
                                                   onchange="toggleHora(this, 'uniforme_<?= $p['id'] ?>')">
                                        </td>
                                        <td>
                                            <input type="time" name="inspecciones[<?= $p['id'] ?>][hora_uniforme]" 
                                                   id="uniforme_<?= $p['id'] ?>"
                                                   value="<?= $p['hora_revision_uniforme'] ?? '' ?>" 
                                                   class="hora-input"
                                                   <?= $p['uniforme'] !== 'completo' ? 'disabled' : '' ?>>
                                        </td>
                                        <td>
                                            <input type="text" name="inspecciones[<?= $p['id'] ?>][obs_uniforme]" 
                                                   value="<?= sanitizar($p['obs_uniforme'] ?? '') ?>" 
                                                   class="obs-input" placeholder="Obs.">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($personal)): ?>
                                    <tr><td colspan="8" style="text-align:center; padding: 20px;">No hay personal en esta sucursal</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Observaciones Generales</label>
                            <textarea name="inspecciones[0][observaciones]" rows="2" placeholder="Observaciones generales del día..."></textarea>
                        </div>
                        
                        <?php if (!empty($personal)): ?>
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">Guardar Inspección</button>
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
