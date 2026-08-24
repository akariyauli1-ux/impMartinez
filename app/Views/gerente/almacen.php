<?php $titulo = 'Almacen'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($estado_almacen) ?></div>
        <div class="stat-label">Sucursales</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= array_sum(array_column($estado_almacen, 'total_repuestos')) ?></div>
        <div class="stat-label">Total Repuestos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= array_sum(array_column($estado_almacen, 'stock_total')) ?></div>
        <div class="stat-label">Stock Total</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= array_sum(array_column($estado_almacen, 'stock_bajo')) ?></div>
        <div class="stat-label">Stock Bajo</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Estado de Almacen por Sucursal</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Sucursal</th>
                    <th>Total Repuestos</th>
                    <th>Stock Total</th>
                    <th>Stock Bajo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estado_almacen)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">No hay datos de almacen</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($estado_almacen as $almacen): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($almacen['sucursal']) ?></strong></td>
                        <td><?= $almacen['total_repuestos'] ?></td>
                        <td><?= $almacen['stock_total'] ?></td>
                        <td>
                            <?php if ($almacen['stock_bajo'] > 0): ?>
                                <span class="badge badge-rojo"><?= $almacen['stock_bajo'] ?></span>
                            <?php else: ?>
                                <span class="badge badge-verde">0</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($almacen['stock_bajo'] > 3): ?>
                                <span class="badge badge-rojo">Critico</span>
                            <?php elseif ($almacen['stock_bajo'] > 0): ?>
                                <span class="badge badge-amarillo">Atencion</span>
                            <?php else: ?>
                                <span class="badge badge-verde">OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Repuestos Mas Pedidos</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Repuesto</th>
                    <th>Total Pedidos</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mas_pedidos)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px;">No hay pedidos registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($mas_pedidos as $index => $pedido): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><strong><?= htmlspecialchars($pedido['nombre']) ?></strong></td>
                        <td><span class="badge badge-negro"><?= $pedido['total_pedidos'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
