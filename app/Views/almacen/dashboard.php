<?php $titulo = 'Dashboard Almacen'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $total_repuestos ?? 0 ?></div>
        <div class="stat-label">Total Repuestos</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= $stock_bajo ?? 0 ?></div>
        <div class="stat-label">Stock Bajo</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($pedidos_sucursal ?? []) ?></div>
        <div class="stat-label">Sucursales</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    <div class="card">
        <div class="card-header">
            <h2>Sucursales con Mas Pedidos</h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Sucursal</th>
                        <th>Pedidos</th>
                        <th>Unidades</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos_sucursal)): ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">Sin datos</td></tr>
                    <?php else: ?>
                        <?php foreach ($pedidos_sucursal as $ps): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ps['nombre'] ?? '') ?></strong></td>
                            <td><span class="badge badge-negro"><?= $ps['total_pedidos'] ?? 0 ?></span></td>
                            <td><?= $ps['total_unidades'] ?? 0 ?></td>
                            <td>S/ <?= number_format($ps['total_monto'] ?? 0, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Repuestos Mas Solicitados</h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th>Marca</th>
                        <th>Solicitudes</th>
                        <th>Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mas_solicitados)): ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">Sin datos</td></tr>
                    <?php else: ?>
                        <?php foreach ($mas_solicitados as $ms): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ms['nombre'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($ms['marca'] ?? '-') ?></td>
                            <td><span class="badge badge-negro"><?= $ms['solicitudes'] ?? 0 ?></span></td>
                            <td><span class="badge badge-verde"><?= $ms['ventas'] ?? 0 ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Acciones Rapidas</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= APP_URL ?>/public/almacen/inventario" class="btn btn-primary">Inventario</a>
        <a href="<?= APP_URL ?>/public/almacen/pedidos" class="btn btn-secondary">Pedidos</a>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
