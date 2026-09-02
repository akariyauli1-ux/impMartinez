<?php $titulo = 'Mis Pedidos de Almacen'; ob_start(); ?>

<style>
.alerta-pendiente {
    background: #FFF3E0;
    border: 2px solid #FF9800;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.alerta-pendiente .icono {
    font-size: 1.8rem;
}
.alerta-pendiente .texto {
    flex: 1;
}
.alerta-pendiente .texto strong {
    color: #E65100;
    font-size: 1rem;
}
.alerta-pendiente .texto p {
    color: #BF360C;
    font-size: 0.85rem;
    margin-top: 2px;
}

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

.respuesta-card {
    background: white;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 12px;
    border-left: 4px solid var(--rojo);
    box-shadow: var(--sombra);
}
.respuesta-card.enviando { border-left-color: #1565C0; }
.respuesta-card.no_existe { border-left-color: #C62828; }
.respuesta-card.stock_agotado { border-left-color: #F57F17; }

.respuesta-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}
.respuesta-repuesto {
    font-weight: 700;
    font-size: 1rem;
}
.respuesta-fecha {
    font-size: 0.8rem;
    color: var(--gris);
}
.respuesta-body {
    font-size: 0.9rem;
    color: var(--gris-oscuro);
    margin-bottom: 10px;
}
.respuesta-acciones {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-confirmar {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-confirmar-verde {
    background: #4CAF50;
    color: white;
}
.btn-confirmar-verde:hover {
    background: #388E3C;
}
.btn-confirmar-gris {
    background: #9E9E9E;
    color: white;
}
.btn-confirmar-gris:hover {
    background: #757575;
}

.historial-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid var(--gris-claro);
}
.historial-item:last-child {
    border-bottom: none;
}
.historial-info {
    flex: 1;
}
.historial-repuesto {
    font-weight: 600;
    font-size: 0.95rem;
}
.historial-detalle {
    font-size: 0.8rem;
    color: var(--gris);
    margin-top: 2px;
}
.historial-fecha {
    font-size: 0.8rem;
    color: var(--gris);
    text-align: right;
}
</style>

<?php if (!empty($pendientes_confirmacion)): ?>
<div class="alerta-pendiente">
    <div class="icono">&#128276;</div>
    <div class="texto">
        <strong>Tienes <?= count($pendientes_confirmacion) ?> pedido(s) pendientes de confirmacion</strong>
        <p>Almacen ha respondido a tus pedidos. Por favor confirma.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Respuestas de Almacen - Pendientes de Confirmar</h2>
    </div>
    <div style="padding: 16px;">
        <?php foreach ($pendientes_confirmacion as $pc): ?>
            <?php
            $estado_labels = [
                'enviando' => 'Enviando',
                'no_existe' => 'No Existe',
                'stock_agotado' => 'Stock Agotado',
            ];
            $label = $estado_labels[$pc['estado']] ?? $pc['estado'];
            ?>
            <div class="respuesta-card <?= $pc['estado'] ?>">
                <div class="respuesta-header">
                    <span class="respuesta-repuesto">
                        <?= htmlspecialchars($pc['repuesto_nombre'] ?? '') ?>
                        <?php if (!empty($pc['marca'])): ?>
                            - <?= htmlspecialchars($pc['marca']) ?>
                        <?php endif; ?>
                    </span>
                    <span class="estado-badge estado-<?= $pc['estado'] ?>"><?= $label ?></span>
                </div>
                <div class="respuesta-body">
                    <strong>Cantidad solicitada:</strong> <?= $pc['cantidad'] ?><br>
                    <strong>Respondido por:</strong> <?= htmlspecialchars(($pc['respondido_nombre'] ?? '') . ' ' . ($pc['respondido_ap'] ?? '')) ?><br>
                    <strong>Fecha respuesta:</strong> <?= date('d/m/Y H:i', strtotime($pc['fecha_respuesta'])) ?>
                    <?php if (!empty($pc['respuesta_texto'])): ?>
                        <br><strong>Nota:</strong> <?= htmlspecialchars($pc['respuesta_texto']) ?>
                    <?php endif; ?>
                </div>
                <div class="respuesta-acciones">
                    <?php if ($pc['estado'] === 'enviando'): ?>
                        <form method="POST" action="<?= APP_URL ?>/public/pedidos/confirmar-recibido" style="display: inline;">
                            <input type="hidden" name="pedido_id" value="<?= $pc['id'] ?>">
                            <button type="submit" class="btn-confirmar btn-confirmar-verde">
                                &#10003; Ya llego el pedido
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= APP_URL ?>/public/pedidos/confirmar-leido" style="display: inline;">
                            <input type="hidden" name="pedido_id" value="<?= $pc['id'] ?>">
                            <button type="submit" class="btn-confirmar btn-confirmar-gris">
                                &#10003; Confirmar leido
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Mis Pedidos</h2>
        <a href="<?= APP_URL ?>/public/pedidos/nuevo" class="btn btn-primary btn-sm">+ Nuevo Pedido</a>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
                    <th>Respuesta</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mis_pedidos)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--gris);">
                        No tienes pedidos registrados
                    </td>
                </tr>
                <?php else: ?>
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
                    <tr>
                        <td>
                            <div style="font-size: 0.85rem;"><?= date('d/m/Y', strtotime($mp['fecha_solicitud'])) ?></div>
                            <div style="font-size: 0.75rem; color: var(--gris);"><?= date('H:i', strtotime($mp['fecha_solicitud'])) ?></div>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($mp['repuesto_nombre'] ?? '') ?></div>
                            <div style="font-size: 0.8rem; color: var(--gris);">
                                <?= htmlspecialchars($mp['repuesto_codigo'] ?? '') ?>
                                <?php if (!empty($mp['marca'])): ?>
                                    - <?= htmlspecialchars($mp['marca']) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><strong><?= $mp['cantidad'] ?></strong></td>
                        <td><?= htmlspecialchars($mp['sucursal_nombre'] ?? '') ?></td>
                        <td>
                            <span class="estado-badge estado-<?= $mp['estado'] ?>">
                                <?= $estado_labels[$mp['estado']] ?? $mp['estado'] ?>
                            </span>
                            <?php if ($mp['estado'] === 'solicitado'): ?>
                                <div style="font-size: 0.7rem; color: var(--gris); margin-top: 2px;">Esperando respuesta</div>
                            <?php elseif ($mp['estado'] === 'confirmado'): ?>
                                <div style="font-size: 0.7rem; color: #1B5E20; margin-top: 2px;">
                                    <?= !empty($mp['fecha_confirmacion']) ? date('d/m/Y H:i', strtotime($mp['fecha_confirmacion'])) : '' ?>
                                </div>
                            <?php elseif (in_array($mp['estado'], ['enviando', 'no_existe', 'stock_agotado']) && !$mp['confirmado']): ?>
                                <div style="font-size: 0.7rem; color: #E65100; margin-top: 2px;">Pendiente confirmar</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($mp['respuesta_texto'])): ?>
                                <div style="font-size: 0.85rem;"><?= htmlspecialchars($mp['respuesta_texto']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--gris);">
                                    Por: <?= htmlspecialchars(($mp['respondido_nombre'] ?? '') . ' ' . ($mp['respondido_ap'] ?? '')) ?>
                                </div>
                            <?php else: ?>
                                <span style="color: var(--gris);">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
