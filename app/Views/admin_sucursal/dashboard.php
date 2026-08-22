<?php $titulo = 'Dashboard Admin Sucursal'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $pendientes ?></div>
        <div class="stat-label">Equipos Pendientes</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $en_reparacion ?></div>
        <div class="stat-label">En Reparación</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $completados ?></div>
        <div class="stat-label">Completados</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Acciones</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= APP_URL ?>/public/admin-sucursal/pendientes" class="btn btn-primary">Ver Pendientes</a>
        <a href="<?= APP_URL ?>/public/admin-sucursal/asignar" class="btn btn-secondary">Asignar a Sucursal</a>
        <a href="<?= APP_URL ?>/public/admin-sucursal/asistencia" class="btn btn-outline">Asistencia</a>
        <a href="<?= APP_URL ?>/public/admin-sucursal/inspecciones" class="btn btn-outline">Inspecciones</a>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
