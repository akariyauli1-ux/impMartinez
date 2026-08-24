<?php $titulo = 'Dashboard Recepción'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $hoy ?></div>
        <div class="stat-label">Registros de Hoy</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $mis_registros ?></div>
        <div class="stat-label">Total Registrados</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $pendientes ?></div>
        <div class="stat-label">Pendientes de Asignar</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Acciones Rápidas</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= APP_URL ?>/public/recepcion/nuevo-equipo" class="btn btn-primary">Registrar Cliente y Equipo</a>
        <a href="<?= APP_URL ?>/public/recepcion/mis-registros" class="btn btn-outline">Ver Mis Registros</a>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
