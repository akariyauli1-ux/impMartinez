<?php $titulo = 'Reporte de Inspecciones'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Filtros de Búsqueda</h2>
    </div>
    <form method="GET" action="<?= APP_URL ?>/public/gerente/inspecciones" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
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
        <h2>Reporte de Inspecciones - <?= date('d/m/Y', strtotime($fecha)) ?></h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Sucursal</th>
                    <th>Cargo</th>
                    <th>Limpieza</th>
                    <th>Uniforme</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inspecciones)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: var(--gris);">
                            No hay registros de inspecciones para esta fecha
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inspecciones as $i): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($i['nombre'] . ' ' . $i['apellido_paterno']) ?></strong></td>
                            <td><?= htmlspecialchars($i['sucursal_nombre'] ?? 'Sin asignar') ?></td>
                            <td><span class="badge badge-gris"><?= ucfirst(str_replace('_', ' ', $i['rol'])) ?></span></td>
                            <td>
                                <?php
                                $limpieza_class = 'badge-gris';
                                $limpieza_texto = 'Sin registro';
                                
                                if ($i['limpieza'] === 'aprobado') {
                                    $limpieza_class = 'badge-verde';
                                    $limpieza_texto = 'Aprobado';
                                } elseif ($i['limpieza'] === 'observado') {
                                    $limpieza_class = 'badge-amarillo';
                                    $limpieza_texto = 'Observado';
                                } elseif ($i['limpieza'] === 'rechazado') {
                                    $limpieza_class = 'badge-rojo';
                                    $limpieza_texto = 'Rechazado';
                                }
                                ?>
                                <span class="badge <?= $limpieza_class ?>"><?= $limpieza_texto ?></span>
                            </td>
                            <td>
                                <?php
                                $uniforme_class = 'badge-gris';
                                $uniforme_texto = 'Sin registro';
                                
                                if ($i['uniforme'] === 'completo') {
                                    $uniforme_class = 'badge-verde';
                                    $uniforme_texto = 'Completo';
                                } elseif ($i['uniforme'] === 'incompleto') {
                                    $uniforme_class = 'badge-rojo';
                                    $uniforme_texto = 'Incompleto';
                                } elseif ($i['uniforme'] === 'observado') {
                                    $uniforme_class = 'badge-amarillo';
                                    $uniforme_texto = 'Observado';
                                }
                                ?>
                                <span class="badge <?= $uniforme_class ?>"><?= $uniforme_texto ?></span>
                            </td>
                            <td><?= htmlspecialchars($i['observaciones'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
