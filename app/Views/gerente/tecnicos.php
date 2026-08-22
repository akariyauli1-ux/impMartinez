<?php $titulo = 'Trabajo de Técnicos'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($tecnicos) ?></div>
        <div class="stat-label">Total Técnicos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= array_sum(array_column($tecnicos, 'trabajos')) ?></div>
        <div class="stat-label">Trabajos Asignados</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count(array_filter($tecnicos, function($t) { return $t['trabajos'] < 4; })) ?></div>
        <div class="stat-label">Disponibles</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count(array_filter($tecnicos, function($t) { return $t['trabajos'] >= 4; })) ?></div>
        <div class="stat-label">Con Carga Completa</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Técnicos por Sucursal</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Técnico</th>
                    <th>Sucursal</th>
                    <th>Trabajos Asignados</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tecnicos)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">No hay técnicos registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($tecnicos as $tecnico): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($tecnico['nombre'] . ' ' . $tecnico['apellido_paterno']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($tecnico['sucursal_nombre']) ?></td>
                        <td>
                            <span class="badge badge-negro"><?= $tecnico['trabajos'] ?>/4</span>
                        </td>
                        <td>
                            <?php if ($tecnico['trabajos'] >= 4): ?>
                                <span class="badge badge-rojo">Carga Completa</span>
                            <?php elseif ($tecnico['trabajos'] > 0): ?>
                                <span class="badge badge-amarillo">En Trabajo</span>
                            <?php else: ?>
                                <span class="badge badge-verde">Disponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
