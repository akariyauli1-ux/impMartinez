<?php $titulo = 'Dashboard RRHH'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $total_personal ?></div>
        <div class="stat-label">Total Personal</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $presentes ?></div>
        <div class="stat-label">Presentes Hoy</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $tardanzas ?></div>
        <div class="stat-label">Tardanzas Hoy</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $ausentes ?></div>
        <div class="stat-label">Ausentes Hoy</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Acciones</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= APP_URL ?>/public/usuarios" class="btn btn-primary">Gestion Usuarios</a>
        <a href="<?= APP_URL ?>/public/rrhh/asistencia" class="btn btn-secondary">Reporte Asistencia</a>
        <a href="<?= APP_URL ?>/public/rrhh/inspecciones" class="btn btn-outline">Reporte Inspecciones</a>
        <a href="<?= APP_URL ?>/public/rrhh/productividad" class="btn btn-outline">Productividad</a>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
