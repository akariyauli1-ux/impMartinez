<?php $titulo = 'Reporte de Asistencia'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Filtros de Búsqueda</h2>
    </div>
    <form method="GET" action="<?= APP_URL ?>/public/rrhh/asistencia" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Fecha</label>
            <input type="date" name="fecha" value="<?= $fecha ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Sucursal</label>
            <select name="sucursal" class="form-control">
                <option value="">Todas las sucursales</option>
                <?php foreach ($sucursales as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($_GET['sucursal'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Reporte de Asistencia - <?= date('d/m/Y', strtotime($fecha)) ?></h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Sucursal</th>
                    <th>Cargo</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Horas Trabajadas</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asistencias)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: var(--gris);">
                            No hay registros de asistencia para esta fecha
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($asistencias as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido_paterno']) ?></strong></td>
                            <td><?= htmlspecialchars($a['sucursal_nombre'] ?? 'Sin asignar') ?></td>
                            <td><span class="badge badge-gris"><?= ucfirst(str_replace('_', ' ', $a['rol'])) ?></span></td>
                            <td><?= $a['hora_entrada'] ?? '-' ?></td>
                            <td><?= $a['hora_salida'] ?? '-' ?></td>
                            <td>
                                <?php
                                if ($a['hora_entrada'] && $a['hora_salida']) {
                                    $entrada = strtotime($a['hora_entrada']);
                                    $salida = strtotime($a['hora_salida']);
                                    $diferencia = $salida - $entrada;
                                    $horas = floor($diferencia / 3600);
                                    $minutos = floor(($diferencia % 3600) / 60);
                                    echo $horas . 'h ' . $minutos . 'm';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $estado_class = 'badge-gris';
                                $estado_texto = 'Sin registro';
                                
                                if ($a['estado'] === 'presente') {
                                    $estado_class = 'badge-verde';
                                    $estado_texto = 'Presente';
                                } elseif ($a['estado'] === 'tardanza') {
                                    $estado_class = 'badge-amarillo';
                                    $estado_texto = 'Tardanza';
                                } elseif ($a['estado'] === 'ausente') {
                                    $estado_class = 'badge-rojo';
                                    $estado_texto = 'Ausente';
                                } elseif ($a['estado'] === 'permiso') {
                                    $estado_class = 'badge-azul';
                                    $estado_texto = 'Permiso';
                                }
                                ?>
                                <span class="badge <?= $estado_class ?>"><?= $estado_texto ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>