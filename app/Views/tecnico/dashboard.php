<?php $titulo = 'Dashboard Tecnico'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $mis_trabajos ?>/4</div>
        <div class="stat-label">Trabajos Asignados</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $disponibles ?></div>
        <div class="stat-label">Disponibles</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Mis Trabajos</h2>
    </div>
    <a href="<?= APP_URL ?>/public/tecnico/mis-trabajos" class="btn btn-primary">Ver Mis Trabajos</a>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
