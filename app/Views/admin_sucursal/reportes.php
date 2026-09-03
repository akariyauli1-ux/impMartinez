<?php $titulo = 'Reportes de Equipos'; ob_start(); ?>

<style>
.filtros-container {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}
.filtros-container h3 {
    margin: 0 0 10px 0;
    font-size: 1em;
    color: #333;
}
.filtros-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: end;
}
.filtros-row .form-group {
    margin: 0;
    min-width: 120px;
}
.filtros-row label {
    font-size: 0.85em;
    margin-bottom: 3px;
    display: block;
}
.filtros-row select {
    padding: 6px 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 0.9em;
}
.btn-filtrar {
    background: #2196F3;
    color: white;
    border: none;
    padding: 7px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
}
.btn-filtrar:hover {
    background: #1976D2;
}
.btn-limpiar {
    background: #757575;
    color: white;
    border: none;
    padding: 7px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
    text-decoration: none;
    display: inline-block;
}
.btn-limpiar:hover {
    background: #616161;
}
</style>

<!-- Filtros -->
<div class="filtros-container">
    <h3>🔍 Filtrar por Fecha</h3>
    <form method="GET" action="<?= APP_URL ?>/public/admin-sucursal/reportes">
        <div class="filtros-row">
            <div class="form-group">
                <label>Día</label>
                <select name="dia">
                    <option value="">Todos</option>
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <option value="<?= $i ?>" <?= ($filtros['dia'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Mes</label>
                <select name="mes">
                    <option value="">Todos</option>
                    <?php 
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    for ($i = 1; $i <= 12; $i++): 
                    ?>
                        <option value="<?= $i ?>" <?= ($filtros['mes'] ?? '') == $i ? 'selected' : '' ?>><?= $meses[$i-1] ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Año</label>
                <select name="anio">
                    <option value="">Todos</option>
                    <?php 
                    $anio_actual = date('Y');
                    for ($i = $anio_actual; $i >= $anio_actual - 5; $i--): 
                    ?>
                        <option value="<?= $i ?>" <?= ($filtros['anio'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn-filtrar">Filtrar</button>
                <a href="<?= APP_URL ?>/public/admin-sucursal/reportes" class="btn-limpiar">Limpiar</a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Reporte de Estados de Equipos</h2>
        <?php
        $hay_filtros = !empty($filtros['dia']) || !empty($filtros['mes']) || !empty($filtros['anio']);
        if ($hay_filtros):
        ?>
        <span style="background: #FF9800; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8em;">
            🔍 Filtros activos
        </span>
        <?php endif; ?>
    </div>
    
    <?php if (empty($reportes)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay equipos registrados en esta sucursal<?= $hay_filtros ? ' para el período seleccionado' : '' ?>.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <?php foreach ($reportes as $reporte): ?>
                <div class="stat-card">
                    <div class="stat-value"><?= $reporte['cantidad'] ?></div>
                    <div class="stat-label"><?= ucfirst(str_replace('_', ' ', $reporte['estado'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = array_sum(array_column($reportes, 'cantidad'));
                foreach ($reportes as $reporte): 
                    $porcentaje = $total > 0 ? round(($reporte['cantidad'] / $total) * 100, 1) : 0;
                ?>
                    <tr>
                        <td><?= ucfirst(str_replace('_', ' ', $reporte['estado'])) ?></td>
                        <td><?= $reporte['cantidad'] ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; background: #e0e0e0; height: 20px; border-radius: 4px; overflow: hidden;">
                                    <div style="width: <?= $porcentaje ?>%; height: 100%; background: #007bff; transition: width 0.3s;"></div>
                                </div>
                                <span><?= $porcentaje ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px; text-align: right;">
            <strong>Total de equipos: <?= $total ?></strong>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
