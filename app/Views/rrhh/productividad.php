<?php $titulo = 'Productividad del Personal'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Filtros de Búsqueda</h2>
    </div>
    <form method="GET" action="<?= APP_URL ?>/public/rrhh/productividad" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
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
        <h2>Productividad de Técnicos</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Técnico</th>
                    <th>Sucursal</th>
                    <th>Trabajos Activos</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tecnicos)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: var(--gris);">
                            No se encontraron técnicos registrados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tecnicos as $t): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido_paterno']) ?></strong></td>
                            <td><?= htmlspecialchars($t['sucursal_nombre'] ?? 'Sin asignar') ?></td>
                            <td>
                                <span class="badge badge-azul"><?= $t['trabajos'] ?? 0 ?> trabajos</span>
                            </td>
                            <td>
                                <?php
                                $trabajos = $t['trabajos'] ?? 0;
                                if ($trabajos == 0) {
                                    echo '<span class="badge badge-verde">Disponible</span>';
                                } elseif ($trabajos <= 2) {
                                    echo '<span class="badge badge-azul">Carga baja</span>';
                                } elseif ($trabajos <= 3) {
                                    echo '<span class="badge badge-amarillo">Carga media</span>';
                                } else {
                                    echo '<span class="badge badge-rojo">Carga alta</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>