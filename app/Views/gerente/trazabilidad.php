<?php $titulo = 'Trazabilidad de Equipos'; ob_start(); ?>

<style>
.estado-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.estado-registrado { background: #E3F2FD; color: #1565C0; }
.estado-pendiente_asignacion { background: #FFF3E0; color: #E65100; }
.estado-asignado_sucursal { background: #F3E5F5; color: #6A1B9A; }
.estado-recibido { background: #E8F5E9; color: #2E7D32; }
.estado-en_reparacion { background: #FFF8E1; color: #F57F17; }
.estado-completado { background: #E0F7FA; color: #00695C; }
.estado-entregado { background: #E8F5E9; color: #1B5E20; }

.btn-ver-timeline {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: var(--rojo);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 600;
    transition: background 0.3s;
}
.btn-ver-timeline:hover {
    background: var(--rojo-oscuro);
    color: white;
}

.timeline-mini {
    display: flex;
    align-items: center;
    gap: 2px;
    margin-top: 4px;
}
.timeline-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--gris-claro);
}
.timeline-dot.active {
    background: var(--rojo);
}
.timeline-line {
    width: 12px;
    height: 2px;
    background: var(--gris-claro);
}
.timeline-line.active {
    background: var(--rojo);
}

.filtro-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--sombra);
}
</style>

<div class="filtro-card">
    <form method="GET" action="<?= APP_URL ?>/public/gerente/trazabilidad" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label>Buscar</label>
            <input type="text" name="busqueda" value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>" placeholder="Cliente, marca, modelo..." class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label>Estado</label>
            <select name="estado" class="form-control">
                <option value="">Todos</option>
                <option value="registrado" <?= ($filtros['estado'] ?? '') === 'registrado' ? 'selected' : '' ?>>Registrado</option>
                <option value="pendiente_asignacion" <?= ($filtros['estado'] ?? '') === 'pendiente_asignacion' ? 'selected' : '' ?>>Pendiente Asignacion</option>
                <option value="asignado_sucursal" <?= ($filtros['estado'] ?? '') === 'asignado_sucursal' ? 'selected' : '' ?>>Asignado a Sucursal</option>
                <option value="recibido" <?= ($filtros['estado'] ?? '') === 'recibido' ? 'selected' : '' ?>>Recibido</option>
                <option value="en_reparacion" <?= ($filtros['estado'] ?? '') === 'en_reparacion' ? 'selected' : '' ?>>En Reparacion</option>
                <option value="completado" <?= ($filtros['estado'] ?? '') === 'completado' ? 'selected' : '' ?>>Completado</option>
                <option value="entregado" <?= ($filtros['estado'] ?? '') === 'entregado' ? 'selected' : '' ?>>Entregado</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label>Sucursal</label>
            <select name="sucursal" class="form-control">
                <option value="">Todas</option>
                <?php foreach ($sucursales as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($filtros['sucursal_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
            <label>Desde</label>
            <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
            <label>Hasta</label>
            <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="<?= APP_URL ?>/public/gerente/trazabilidad" class="btn btn-outline">Limpiar</a>
        </div>
    </form>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
    <?php
    $estados_count = [
        'registrado' => 0,
        'pendiente_asignacion' => 0,
        'asignado_sucursal' => 0,
        'recibido' => 0,
        'en_reparacion' => 0,
        'completado' => 0,
        'entregado' => 0,
    ];
    foreach ($equipos as $eq) {
        if (isset($estados_count[$eq['estado']])) {
            $estados_count[$eq['estado']]++;
        }
    }
    ?>
    <div class="stat-card" style="padding: 12px;">
        <div class="stat-value" style="font-size: 1.5rem;"><?= count($equipos) ?></div>
        <div class="stat-label" style="font-size: 0.75rem;">Total</div>
    </div>
    <div class="stat-card" style="padding: 12px;">
        <div class="stat-value" style="font-size: 1.5rem; color: #E65100;"><?= $estados_count['pendiente_asignacion'] ?></div>
        <div class="stat-label" style="font-size: 0.75rem;">Pendientes</div>
    </div>
    <div class="stat-card" style="padding: 12px;">
        <div class="stat-value" style="font-size: 1.5rem; color: #F57F17;"><?= $estados_count['en_reparacion'] + $estados_count['recibido'] + $estados_count['asignado_sucursal'] ?></div>
        <div class="stat-label" style="font-size: 0.75rem;">En Proceso</div>
    </div>
    <div class="stat-card" style="padding: 12px;">
        <div class="stat-value" style="font-size: 1.5rem; color: #00695C;"><?= $estados_count['completado'] ?></div>
        <div class="stat-label" style="font-size: 0.75rem;">Completados</div>
    </div>
    <div class="stat-card" style="padding: 12px;">
        <div class="stat-value" style="font-size: 1.5rem; color: #1B5E20;"><?= $estados_count['entregado'] ?></div>
        <div class="stat-label" style="font-size: 0.75rem;">Entregados</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Equipos - Trazabilidad Completa</h2>
        <span style="color: var(--gris); font-size: 0.9rem;"><?= count($equipos) ?> equipo(s) encontrado(s)</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Equipo</th>
                    <th>Cliente</th>
                    <th>Recepcionista</th>
                    <th>Sucursal Actual</th>
                    <th>Jefe Tecnico</th>
                    <th>Tecnico</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($equipos)): ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 30px; color: var(--gris);">
                        No se encontraron equipos con los filtros seleccionados
                    </td>
                </tr>
                <?php else: ?>
                    <?php
                    $estado_labels = [
                        'registrado' => 'Registrado',
                        'pendiente_asignacion' => 'Pendiente Asignacion',
                        'asignado_sucursal' => 'Asignado a Sucursal',
                        'recibido' => 'Recibido',
                        'en_reparacion' => 'En Reparacion',
                        'completado' => 'Completado',
                        'entregado' => 'Entregado',
                    ];
                    $estados_flujo = ['registrado', 'pendiente_asignacion', 'asignado_sucursal', 'recibido', 'en_reparacion', 'completado', 'entregado'];
                    ?>
                    <?php foreach ($equipos as $eq): ?>
                    <?php
                    $estado_actual_index = array_search($eq['estado'], $estados_flujo);
                    ?>
                    <tr>
                        <td><strong>#<?= $eq['id'] ?></strong></td>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($eq['tipo_equipo']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--gris);">
                                <?= htmlspecialchars($eq['marca'] ?? '') ?> <?= htmlspecialchars($eq['modelo'] ?? '') ?>
                            </div>
                            <?php if (!empty($eq['numero_serie'])): ?>
                            <div style="font-size: 0.75rem; color: var(--gris);">
                                S/N: <?= htmlspecialchars($eq['numero_serie']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($eq['cliente_nombre'] . ' ' . $eq['cliente_ap']) ?></div>
                            <?php if (!empty($eq['cliente_dni'])): ?>
                            <div style="font-size: 0.8rem; color: var(--gris);">DNI: <?= htmlspecialchars($eq['cliente_dni']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($eq['cliente_tel'])): ?>
                            <div style="font-size: 0.8rem; color: var(--gris);"><?= htmlspecialchars($eq['cliente_tel']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($eq['recepcionista_nombre'])): ?>
                                <span style="font-size: 0.85rem;"><?= htmlspecialchars(trim($eq['recepcionista_nombre'])) ?></span>
                                <div style="font-size: 0.75rem; color: var(--gris);"><?= date('d/m/Y H:i', strtotime($eq['fecha_registro'])) ?></div>
                            <?php else: ?>
                                <span style="color: var(--gris);">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($eq['sucursal_actual_nombre'])): ?>
                                <span style="font-size: 0.85rem;"><?= htmlspecialchars($eq['sucursal_actual_nombre']) ?></span>
                            <?php else: ?>
                                <span style="color: var(--gris);">Sin asignar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($eq['jefe_tecnico_nombre'])): ?>
                                <span style="font-size: 0.85rem;"><?= htmlspecialchars(trim($eq['jefe_tecnico_nombre'])) ?></span>
                            <?php else: ?>
                                <span style="color: var(--gris);">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($eq['tecnico_nombre'])): ?>
                                <span style="font-size: 0.85rem;"><?= htmlspecialchars(trim($eq['tecnico_nombre'])) ?></span>
                                <?php if (!empty($eq['fecha_asignacion_tecnico'])): ?>
                                <div style="font-size: 0.75rem; color: var(--gris);"><?= date('d/m/Y H:i', strtotime($eq['fecha_asignacion_tecnico'])) ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: var(--gris);">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="estado-badge estado-<?= $eq['estado'] ?>">
                                <?= $estado_labels[$eq['estado']] ?? $eq['estado'] ?>
                            </span>
                            <div class="timeline-mini" title="Progreso del equipo">
                                <?php foreach ($estados_flujo as $i => $estado_flujo): ?>
                                    <?php if ($i > 0): ?>
                                    <div class="timeline-line <?= $i <= $estado_actual_index ? 'active' : '' ?>"></div>
                                    <?php endif; ?>
                                    <div class="timeline-dot <?= $i <= $estado_actual_index ? 'active' : '' ?>" title="<?= $estado_labels[$estado_flujo] ?>"></div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;"><?= date('d/m/Y', strtotime($eq['fecha_registro'])) ?></div>
                            <div style="font-size: 0.75rem; color: var(--gris);"><?= date('H:i', strtotime($eq['fecha_registro'])) ?></div>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/public/gerente/trazabilidad-detalle?id=<?= $eq['id'] ?>" class="btn-ver-timeline">
                                Ver Historial
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
