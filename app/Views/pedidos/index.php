<?php $titulo = 'Ventas'; $css_extra = 'pedidos.css'; ob_start(); ?>


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
                    <?php if ($pc['estado'] === 'stock_agotado'): ?>
                        <br><strong style="color: #C62828;">YA NO HAY DISPONIBLE EN ALMACEN</strong>
                    <?php endif; ?>
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
        <h2>Mis Ventas</h2>
        <a href="<?= APP_URL ?>/public/pedidos/nuevo" class="btn btn-primary btn-sm">+ Nueva Venta</a>
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
                        No tienes ventas registradas
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
                                <div style="font-size: 0.7rem; color: var(--gris); margin-top: 2px;">Pendiente de procesar</div>
                            <?php elseif ($mp['estado'] === 'stock_agotado'): ?>
                                <div style="font-size: 0.7rem; color: #C62828; margin-top: 2px; font-weight: 600;">YA NO HAY DISPONIBLE EN ALMACEN</div>
                            <?php elseif ($mp['estado'] === 'confirmado'): ?>
                                <div style="font-size: 0.7rem; color: #1B5E20; margin-top: 2px;">
                                    <?= !empty($mp['fecha_confirmacion']) ? date('d/m/Y H:i', strtotime($mp['fecha_confirmacion'])) : '' ?>
                                </div>
                            <?php elseif (in_array($mp['estado'], ['enviando', 'no_existe', 'stock_agotado']) && !$mp['confirmado']): ?>
                                <div style="font-size: 0.7rem; color: #E65100; margin-top: 2px;">Pendiente confirmar recepción</div>
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
