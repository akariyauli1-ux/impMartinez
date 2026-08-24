<?php $titulo = 'Dashboard Jefe Técnico'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($tecnicos) ?></div>
        <div class="stat-label">Técnicos Disponibles</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $pendientes ?></div>
        <div class="stat-label">Equipos Pendientes</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Acciones Rápidas</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= APP_URL ?>/public/jefe-tecnico/asignar-tecnicos" class="btn btn-primary">Asignar Equipos</a>
        <a href="<?= APP_URL ?>/public/jefe-tecnico/seguimiento" class="btn btn-secondary">Ver Seguimiento</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Estado de Técnicos</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Trabajos Activos</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tecnicos)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px;">No hay técnicos registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tecnicos as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido_paterno']) ?></td>
                            <td><?= $t['trabajos'] ?? 0 ?>/4</td>
                            <td>
                                <?php if (($t['trabajos'] ?? 0) >= 4): ?>
                                    <span class="badge badge-rojo">Carga Completa</span>
                                <?php elseif (($t['trabajos'] ?? 0) > 0): ?>
                                    <span class="badge badge-amarillo">Trabajando</span>
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
