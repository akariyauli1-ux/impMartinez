<?php $titulo = 'Lotes FIFO - ' . ($repuesto['nombre'] ?? ''); ob_start(); ?>

<style>
.lotes-header {
    background: linear-gradient(135deg, #1565C0, #1976D2);
    color: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.lotes-header h2 {
    margin: 0 0 10px 0;
}
.precios-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 15px;
}
.precio-card {
    background: rgba(255,255,255,0.15);
    padding: 12px;
    border-radius: 6px;
    text-align: center;
}
.precio-card .label {
    font-size: 0.85rem;
    opacity: 0.9;
}
.precio-card .value {
    font-size: 1.4rem;
    font-weight: 700;
    margin-top: 4px;
}
.lote-activo {
    background: #E8F5E9 !important;
}
.lote-agotado {
    opacity: 0.5;
}
.badge-fifo {
    background: #1565C0;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
}
</style>

<div class="lotes-header">
    <div style="display: flex; justify-content: space-between; align-items: start;">
        <div>
            <h2><?= htmlspecialchars($repuesto['nombre'] ?? '') ?></h2>
            <p style="margin: 0; opacity: 0.9;">
                Codigo: <?= htmlspecialchars($repuesto['codigo'] ?? '-') ?> | 
                Marca: <?= htmlspecialchars($repuesto['marca'] ?? '-') ?> | 
                Categoria: <?= htmlspecialchars($repuesto['categoria'] ?? '-') ?>
            </p>
        </div>
        <a href="<?= APP_URL ?>/public/almacen/inventario" class="btn btn-outline" style="color: white; border-color: white;">Volver al Inventario</a>
    </div>
    <div class="precios-grid">
        <div class="precio-card">
            <div class="label">Precio Anterior (1er lote)</div>
            <div class="value">S/ <?= number_format($precio_anterior ?? 0, 2) ?></div>
        </div>
        <div class="precio-card">
            <div class="label">Precio Actual (ultimo lote)</div>
            <div class="value">S/ <?= number_format($precio_actual ?? 0, 2) ?></div>
        </div>
        <div class="precio-card">
            <div class="label">Precio Promedio Ponderado</div>
            <div class="value">S/ <?= number_format($precio_promedio ?? 0, 2) ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Lotes de Inventario (FIFO)</h2>
    </div>
    
    <div style="background: #E3F2FD; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
        <small style="color: #1565C0;">
            <strong>Metodo FIFO (First In, First Out):</strong> El stock se despacha en orden de entrada. Primero se consumen los lotes mas antiguos. Cada lote conserva su precio de compra original.
        </small>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha de Entrada</th>
                    <th>Cantidad Original</th>
                    <th>Cantidad Restante</th>
                    <th>Precio de Compra</th>
                    <th>Valor del Lote</th>
                    <th>Almacenista</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lotes)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">No hay lotes registrados para este repuesto</td>
                </tr>
                <?php else: ?>
                    <?php $num = 1; foreach ($lotes as $lote): ?>
                    <tr class="<?= $lote['cantidad_restante'] > 0 ? 'lote-activo' : 'lote-agotado' ?>">
                        <td>
                            <strong><?= $num ?></strong>
                            <?php if ($lote['cantidad_restante'] > 0): ?>
                                <span class="badge-fifo">FIFO</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($lote['fecha_entrada'])) ?></td>
                        <td><?= $lote['cantidad'] ?></td>
                        <td>
                            <strong style="color: <?= $lote['cantidad_restante'] > 0 ? '#2E7D32' : '#C62828' ?>">
                                <?= $lote['cantidad_restante'] ?>
                            </strong>
                        </td>
                        <td><strong>S/ <?= number_format($lote['precio_compra'], 2) ?></strong></td>
                        <td>S/ <?= number_format($lote['cantidad_restante'] * $lote['precio_compra'], 2) ?></td>
                        <td><?= htmlspecialchars($lote['almacenista_nombre'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($lote['motivo'] ?? '-') ?></td>
                        <td>
                            <?php if ($lote['cantidad_restante'] <= 0): ?>
                                <span class="badge badge-descontinuado">Agotado</span>
                            <?php elseif ($lote['cantidad_restante'] < $lote['cantidad']): ?>
                                <span class="badge" style="background: #FF9800; color: white;">Parcial</span>
                            <?php else: ?>
                                <span class="badge badge-activo">Completo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $num++; endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
