<?php $titulo = 'Reportes de Equipos'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Reporte de Estados de Equipos</h2>
    </div>
    
    <?php if (empty($reportes)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay equipos registrados en esta sucursal.</p>
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
