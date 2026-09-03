<?php $titulo = 'Historial de Ventas'; ob_start(); ?>

<style>
.estado-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.estado-solicitado { background: #FFF3E0; color: #E65100; }
.estado-enviando { background: #E3F2FD; color: #1565C0; }
.estado-no_existe { background: #FFEBEE; color: #C62828; }
.estado-stock_agotado { background: #FFF8E1; color: #F57F17; }
.estado-enviado { background: #F3E5F5; color: #6A1B9A; }
.estado-confirmado { background: #E8F5E9; color: #1B5E20; }

.filtro-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--sombra);
}

.timeline-pedido {
    position: relative;
    padding-left: 30px;
}
.timeline-pedido::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gris-claro);
}

.pedido-item {
    position: relative;
    background: white;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: var(--sombra);
    border-left: 4px solid var(--gris-claro);
}
.pedido-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 20px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--gris);
    border: 2px solid white;
}

.pedido-item.estado-solicitado { border-left-color: #E65100; }
.pedido-item.estado-solicitado::before { background: #E65100; }
.pedido-item.estado-enviando { border-left-color: #1565C0; }
.pedido-item.estado-enviando::before { background: #1565C0; }
.pedido-item.estado-no_existe { border-left-color: #C62828; }
.pedido-item.estado-no_existe::before { background: #C62828; }
.pedido-item.estado-stock_agotado { border-left-color: #F57F17; }
.pedido-item.estado-stock_agotado::before { background: #F57F17; }
.pedido-item.estado-enviado { border-left-color: #6A1B9A; }
.pedido-item.estado-enviado::before { background: #6A1B9A; }
.pedido-item.estado-confirmado { border-left-color: #1B5E20; }
.pedido-item.estado-confirmado::before { background: #1B5E20; }

.pedido-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
    flex-wrap: wrap;
    gap: 8px;
}
.pedido-repuesto {
    font-weight: 700;
    font-size: 1rem;
}
.pedido-codigo {
    font-size: 0.8rem;
    color: var(--gris);
}
.pedido-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 8px;
    margin-top: 10px;
    font-size: 0.85rem;
}
.pedido-info-item {
    display: flex;
    flex-direction: column;
}
.pedido-info-label {
    font-size: 0.7rem;
    color: var(--gris);
    text-transform: uppercase;
    font-weight: 600;
}
.pedido-info-value {
    font-weight: 600;
    color: var(--negro);
}
.pedido-respuesta {
    margin-top: 10px;
    padding: 10px;
    background: var(--blanco-humo);
    border-radius: 6px;
    font-size: 0.85rem;
}
.pedido-respuesta strong {
    color: var(--negro);
}
</style>

<div class="stats-grid">
    <?php
    $total = 0;
    $por_estado = [];
    foreach ($contadores as $c) {
        $por_estado[$c['estado']] = $c['cantidad'];
        $total += $c['cantidad'];
    }
    ?>
    <div class="stat-card">
        <div class="stat-value"><?= $total ?></div>
        <div class="stat-label">Total Ventas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #E65100;"><?= $por_estado['solicitado'] ?? 0 ?></div>
        <div class="stat-label">Solicitados</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #1565C0;"><?= $por_estado['enviando'] ?? 0 ?></div>
        <div class="stat-label">Enviando</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #1B5E20;"><?= $por_estado['confirmado'] ?? 0 ?></div>
        <div class="stat-label">Confirmados</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #C62828;"><?= ($por_estado['no_existe'] ?? 0) + ($por_estado['stock_agotado'] ?? 0) ?></div>
        <div class="stat-label">No Disponibles</div>
    </div>
</div>

<div class="filtro-card">
    <form method="GET" action="<?= APP_URL ?>/public/pedidos/historial" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label>Buscar</label>
            <input type="text" name="busqueda" value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>" placeholder="Repuesto, codigo, marca..." class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label>Estado</label>
            <select name="estado" class="form-control">
                <option value="">Todos</option>
                <option value="solicitado" <?= ($filtros['estado'] ?? '') === 'solicitado' ? 'selected' : '' ?>>Solicitado</option>
                <option value="enviando" <?= ($filtros['estado'] ?? '') === 'enviando' ? 'selected' : '' ?>>Enviando</option>
                <option value="no_existe" <?= ($filtros['estado'] ?? '') === 'no_existe' ? 'selected' : '' ?>>No Existe</option>
                <option value="stock_agotado" <?= ($filtros['estado'] ?? '') === 'stock_agotado' ? 'selected' : '' ?>>Stock Agotado</option>
                <option value="enviado" <?= ($filtros['estado'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                <option value="confirmado" <?= ($filtros['estado'] ?? '') === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
            <label>Desde</label>
            <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
            <label>Hasta</label>
            <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="<?= APP_URL ?>/public/pedidos/historial" class="btn btn-outline">Limpiar</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Todas Mis Ventas</h2>
        <span style="color: var(--gris); font-size: 0.9rem;"><?= count($mis_pedidos) ?> registro(s)</span>
    </div>
    
    <?php if (empty($mis_pedidos)): ?>
        <div style="padding: 40px; text-align: center; color: var(--gris);">
            <p style="font-size: 1.1rem; margin-bottom: 10px;">No tienes ventas registradas</p>
            <a href="<?= APP_URL ?>/public/pedidos/nuevo" class="btn btn-primary">Registrar Nueva Venta</a>
        </div>
    <?php else: ?>
        <div style="padding: 20px;">
            <div class="timeline-pedido">
                <?php
                $estado_labels = [
                    'solicitado' => 'Solicitado',
                    'enviando' => 'Enviando',
                    'no_existe' => 'No Existe',
                    'stock_agotado' => 'Stock Agotado',
                    'enviado' => 'Enviado',
                    'confirmado' => 'Confirmado',
                ];
                ?>
                <?php foreach ($mis_pedidos as $mp): ?>
                    <div class="pedido-item estado-<?= $mp['estado'] ?>">
                        <div class="pedido-header">
                            <div>
                                <div class="pedido-repuesto"><?= htmlspecialchars($mp['repuesto_nombre'] ?? '') ?></div>
                                <div class="pedido-codigo">
                                    <?= htmlspecialchars($mp['repuesto_codigo'] ?? '') ?>
                                    <?php if (!empty($mp['marca'])): ?>
                                        - <?= htmlspecialchars($mp['marca']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="estado-badge estado-<?= $mp['estado'] ?>">
                                <?= $estado_labels[$mp['estado']] ?? $mp['estado'] ?>
                            </span>
                        </div>
                        
                        <div class="pedido-info">
                            <div class="pedido-info-item">
                                <span class="pedido-info-label">Cantidad</span>
                                <span class="pedido-info-value"><?= $mp['cantidad'] ?></span>
                            </div>
                            <div class="pedido-info-item">
                                <span class="pedido-info-label">Sucursal</span>
                                <span class="pedido-info-value"><?= htmlspecialchars($mp['sucursal_nombre'] ?? '') ?></span>
                            </div>
                            <div class="pedido-info-item">
                                <span class="pedido-info-label">Fecha Solicitud</span>
                                <span class="pedido-info-value"><?= date('d/m/Y H:i', strtotime($mp['fecha_solicitud'])) ?></span>
                            </div>
                            <?php if (!empty($mp['fecha_respuesta'])): ?>
                            <div class="pedido-info-item">
                                <span class="pedido-info-label">Fecha Respuesta</span>
                                <span class="pedido-info-value"><?= date('d/m/Y H:i', strtotime($mp['fecha_respuesta'])) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($mp['fecha_confirmacion'])): ?>
                            <div class="pedido-info-item">
                                <span class="pedido-info-label">Fecha Confirmacion</span>
                                <span class="pedido-info-value"><?= date('d/m/Y H:i', strtotime($mp['fecha_confirmacion'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($mp['respuesta_texto']) || !empty($mp['respondido_nombre'])): ?>
                        <div class="pedido-respuesta">
                            <?php if (!empty($mp['respondido_nombre'])): ?>
                                <strong>Respondido por:</strong> <?= htmlspecialchars($mp['respondido_nombre'] . ' ' . $mp['respondido_ap']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($mp['respuesta_texto'])): ?>
                                <strong>Nota:</strong> <?= htmlspecialchars($mp['respuesta_texto']) ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
