<?php $titulo = 'Dashboard General'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($sucursales) ?></div>
        <div class="stat-label">Sucursales Activas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $total_equipos ?></div>
        <div class="stat-label">Total Equipos</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $en_reparacion ?></div>
        <div class="stat-label">En Reparacion</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $completados ?></div>
        <div class="stat-label">Completados</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Acciones Rapidas</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= APP_URL ?>/public/usuarios" class="btn btn-primary">Gestion Usuarios</a>
        <a href="<?= APP_URL ?>/public/gerente/sucursales" class="btn btn-secondary">Sucursales</a>
        <a href="<?= APP_URL ?>/public/gerente/tecnicos" class="btn btn-outline">Tecnicos</a>
        <a href="<?= APP_URL ?>/public/gerente/almacen" class="btn btn-outline">Almacen</a>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
