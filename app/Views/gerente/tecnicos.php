<?php $titulo = 'Trabajo de Técnicos'; ob_start(); ?>

<?php
// Calcular totales generales
$total_realizados = 0;
$total_pendientes = 0;
$total_entregados = 0;
$total_pausa = 0;
foreach ($tecnicos as $t) {
    $stats = $t['estadisticas'] ?? [];
    $total_realizados += ($stats['completados'] ?? 0) + ($stats['entregados'] ?? 0);
    $total_pendientes += $stats['pendientes'] ?? 0;
    $total_entregados += $stats['entregados'] ?? 0;
    $total_pausa += $stats['en_pausa'] ?? 0;
}
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($tecnicos) ?></div>
        <div class="stat-label">Total Técnicos</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #4CAF50;">
        <div class="stat-value" style="color: #4CAF50;"><?= $total_realizados ?></div>
        <div class="stat-label">Trabajos Realizados</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #FF9800;">
        <div class="stat-value" style="color: #FF9800;"><?= $total_pendientes ?></div>
        <div class="stat-label">Pendientes</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #2196F3;">
        <div class="stat-value" style="color: #2196F3;"><?= $total_entregados ?></div>
        <div class="stat-label">Entregados</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #9C27B0;">
        <div class="stat-value" style="color: #9C27B0;"><?= $total_pausa ?></div>
        <div class="stat-label">En Pausa</div>
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
                    <th style="text-align: center;">Realizados</th>
                    <th style="text-align: center;">Pendientes</th>
                    <th style="text-align: center;">Entregados</th>
                    <th style="text-align: center;">En Pausa</th>
                    <th>Carga Actual</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tecnicos)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No hay técnicos registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($tecnicos as $tecnico): 
                        $stats = $tecnico['estadisticas'] ?? [];
                        $realizados = ($stats['completados'] ?? 0) + ($stats['entregados'] ?? 0);
                        $pendientes = $stats['pendientes'] ?? 0;
                        $entregados = $stats['entregados'] ?? 0;
                        $en_pausa = $stats['en_pausa'] ?? 0;
                        $carga_actual = $pendientes + $en_pausa;
                    ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($tecnico['nombre'] . ' ' . $tecnico['apellido_paterno']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($tecnico['sucursal_nombre']) ?></td>
                        <td style="text-align: center;">
                            <span class="badge badge-verde"><?= $realizados ?></span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-amarillo"><?= $pendientes ?></span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-azul"><?= $entregados ?></span>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($en_pausa > 0): ?>
                                <span class="badge badge-morado"><?= $en_pausa ?></span>
                            <?php else: ?>
                                <span style="color: #999;">0</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-negro"><?= $carga_actual ?>/4</span>
                        </td>
                        <td>
                            <?php if ($carga_actual >= 4): ?>
                                <span class="badge badge-rojo">Carga Completa</span>
                            <?php elseif ($carga_actual > 0): ?>
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

<style>
.badge-morado {
    background: #9C27B0;
    color: white;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 0.75em;
    font-weight: bold;
}
</style>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
