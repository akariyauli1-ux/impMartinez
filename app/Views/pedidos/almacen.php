<?php $titulo = 'Gestion de Pedidos - Almacen'; ob_start(); ?>

<style>
.alerta-pedidos {
    background: #FFEBEE;
    border: 2px solid #D32F2F;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}
.alerta-pedidos .icono {
    font-size: 2rem;
}
.alerta-pedidos .texto {
    flex: 1;
}
.alerta-pedidos .texto strong {
    color: #B71C1C;
    font-size: 1.1rem;
}
.alerta-pedidos .texto p {
    color: #C62828;
    font-size: 0.85rem;
    margin-top: 2px;
}
.alerta-pedidos .numero {
    background: #D32F2F;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    font-weight: 700;
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

.pedido-card {
    background: white;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 12px;
    border-left: 4px solid #E65100;
    box-shadow: var(--sombra);
}
.pedido-card.pendiente {
    border-left-color: #D32F2F;
}
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
    font-size: 1.05rem;
}
.pedido-codigo {
    font-size: 0.8rem;
    color: var(--gris);
}
.pedido-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 8px;
    margin-bottom: 12px;
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
.pedido-acciones {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--gris-claro);
}

.btn-responder {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-enviando {
    background: #1565C0;
    color: white;
}
.btn-enviando:hover {
    background: #0D47A1;
}
.btn-no-existe {
    background: #C62828;
    color: white;
}
.btn-no-existe:hover {
    background: #B71C1C;
}
.btn-stock-agotado {
    background: #F57F17;
    color: white;
}
.btn-stock-agotado:hover {
    background: #E65100;
}

.modal-respuesta {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}
.modal-respuesta.active {
    display: flex;
}
.modal-respuesta-content {
    background: white;
    border-radius: 12px;
    padding: 24px;
    max-width: 500px;
    width: 90%;
    box-shadow: var(--sombra-fuerte);
}
.modal-respuesta-content h3 {
    margin-bottom: 16px;
}
</style>

<?php if ($total_pendientes > 0): ?>
<div class="alerta-pedidos">
    <div class="icono">&#128680;</div>
    <div class="texto">
        <strong>Tienes <?= $total_pendientes ?> pedido(s) pendiente(s) de respuesta</strong>
        <p>Los solicitantes estan esperando tu respuesta.</p>
    </div>
    <div class="numero"><?= $total_pendientes ?></div>
</div>
<?php endif; ?>

<?php if (!empty($solicitudes)): ?>
<div class="card" style="border-left: 4px solid #FF9800; margin-bottom: 20px;">
    <div class="card-header" style="background: #FFF3E0;">
        <h2>&#128295; Solicitudes de Componentes de Técnicos</h2>
        <span class="badge badge-amarillo"><?= count($solicitudes) ?></span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Técnico</th>
                    <th>Cliente</th>
                    <th>Equipo</th>
                    <th>Repuesto</th>
                    <th>Cant.</th>
                    <th>Total</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $sol): ?>
                <tr>
                    <td>
                        <div style="font-size: 0.85rem;"><?= date('d/m/Y', strtotime($sol['fecha_solicitud'])) ?></div>
                        <div style="font-size: 0.75rem; color: var(--gris);"><?= date('H:i', strtotime($sol['fecha_solicitud'])) ?></div>
                    </td>
                    <td><strong><?= htmlspecialchars($sol['tecnico_nombre'] . ' ' . $sol['tecnico_ap']) ?></strong></td>
                    <td><?= htmlspecialchars($sol['cliente_nombre'] . ' ' . $sol['cliente_ap']) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($sol['tipo_equipo']) ?></strong><br>
                        <small><?= htmlspecialchars($sol['equipo_marca'] . ' ' . $sol['equipo_modelo']) ?></small>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($sol['repuesto_nombre']) ?></strong><br>
                        <small><?= htmlspecialchars($sol['repuesto_codigo']) ?></small>
                    </td>
                    <td><strong><?= $sol['cantidad'] ?></strong></td>
                    <td><strong>S/ <?= number_format($sol['total'], 2) ?></strong></td>
                    <td><small><?= htmlspecialchars($sol['motivo'] ?? 'Sin observaciones') ?></small></td>
                    <td>
                        <?php 
                        $estado_class = 'badge-gris';
                        $estado_texto = $sol['estado'];
                        if ($sol['estado'] === 'solicitado') {
                            $estado_class = 'badge-amarillo';
                            $estado_texto = 'Solicitado';
                        } elseif ($sol['estado'] === 'enviado') {
                            $estado_class = 'badge-azul';
                            $estado_texto = 'Enviado';
                        } elseif ($sol['estado'] === 'recibido') {
                            $estado_class = 'badge-verde';
                            $estado_texto = 'Recibido';
                        }
                        ?>
                        <span class="badge <?= $estado_class ?>"><?= $estado_texto ?></span>
                    </td>
                    <td>
                        <?php if ($sol['estado'] === 'solicitado'): ?>
                            <form method="POST" action="<?= APP_URL ?>/public/pedidos/entregar-solicitud" style="display: inline;" onsubmit="return confirm('¿Confirmas el envío de este componente al técnico?');">
                                <input type="hidden" name="solicitud_id" value="<?= $sol['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">📤 Enviar</button>
                            </form>
                        <?php elseif ($sol['estado'] === 'enviado'): ?>
                            <span style="color: #1565C0; font-size: 0.85rem;">En camino</span>
                        <?php else: ?>
                            <span style="color: #2E7D32; font-size: 0.85rem;">✓ Recibido</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($pendientes)): ?>
<div class="card">
    <div class="card-header">
        <h2>Pedidos Pendientes de Respuesta</h2>
        <span class="badge badge-rojo"><?= count($pendientes) ?></span>
    </div>
    <div style="padding: 16px;">
        <?php foreach ($pendientes as $p): ?>
            <div class="pedido-card pendiente">
                <div class="pedido-header">
                    <div>
                        <div class="pedido-repuesto"><?= htmlspecialchars($p['repuesto_nombre'] ?? '') ?></div>
                        <div class="pedido-codigo">
                            <?= htmlspecialchars($p['repuesto_codigo'] ?? '') ?>
                            <?= !empty($p['marca']) ? '- ' . htmlspecialchars($p['marca']) : '' ?>
                        </div>
                    </div>
                    <span class="estado-badge estado-solicitado">Solicitado</span>
                </div>
                <div class="pedido-info">
                    <div class="pedido-info-item">
                        <span class="pedido-info-label">Solicitante</span>
                        <span class="pedido-info-value"><?= htmlspecialchars(($p['solicitante_nombre'] ?? '') . ' ' . ($p['solicitante_ap'] ?? '')) ?></span>
                    </div>
                    <div class="pedido-info-item">
                        <span class="pedido-info-label">Sucursal</span>
                        <span class="pedido-info-value"><?= htmlspecialchars($p['sucursal_nombre'] ?? '') ?></span>
                    </div>
                    <div class="pedido-info-item">
                        <span class="pedido-info-label">Cantidad Solicitada</span>
                        <span class="pedido-info-value" style="font-size: 1.1rem;"><?= $p['cantidad'] ?></span>
                    </div>
                    <div class="pedido-info-item">
                        <span class="pedido-info-label">Stock en Almacen</span>
                        <span class="pedido-info-value" style="color: <?= ($p['stock'] ?? 0) >= $p['cantidad'] ? '#2E7D32' : '#C62828' ?>; font-size: 1.1rem;">
                            <?= $p['stock'] ?? 0 ?>
                        </span>
                    </div>
                    <div class="pedido-info-item">
                        <span class="pedido-info-label">Fecha solicitud</span>
                        <span class="pedido-info-value"><?= date('d/m/Y H:i', strtotime($p['fecha_solicitud'])) ?></span>
                    </div>
                </div>
                <div class="pedido-acciones">
                    <button class="btn-responder btn-enviando" onclick="abrirModalRespuesta(<?= $p['id'] ?>, 'enviando', '<?= htmlspecialchars($p['repuesto_nombre']) ?>')">
                        &#128230; Enviando
                    </button>
                    <button class="btn-responder btn-no-existe" onclick="abrirModalRespuesta(<?= $p['id'] ?>, 'no_existe', '<?= htmlspecialchars($p['repuesto_nombre']) ?>')">
                        &#10060; No Existe
                    </button>
                    <button class="btn-responder btn-stock-agotado" onclick="abrirModalRespuesta(<?= $p['id'] ?>, 'stock_agotado', '<?= htmlspecialchars($p['repuesto_nombre']) ?>')">
                        &#9888; Stock Agotado
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Todos los Pedidos</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Repuesto</th>
                    <th>Solicitante</th>
                    <th>Sucursal</th>
                    <th>Cant.</th>
                    <th>Estado</th>
                    <th>Respuesta</th>
                    <th>Confirmado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_pedidos)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: var(--gris);">
                        No hay pedidos registrados
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
                    <?php foreach ($todos_pedidos as $tp): ?>
                    <tr>
                        <td>
                            <div style="font-size: 0.85rem;"><?= date('d/m/Y', strtotime($tp['fecha_solicitud'])) ?></div>
                            <div style="font-size: 0.75rem; color: var(--gris);"><?= date('H:i', strtotime($tp['fecha_solicitud'])) ?></div>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($tp['repuesto_nombre'] ?? '') ?></div>
                            <div style="font-size: 0.8rem; color: var(--gris);">
                                <?= htmlspecialchars($tp['repuesto_codigo'] ?? '') ?>
                                <?= !empty($tp['marca']) ? '- ' . htmlspecialchars($tp['marca']) : '' ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars(($tp['solicitante_nombre'] ?? '') . ' ' . ($tp['solicitante_ap'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($tp['sucursal_nombre'] ?? '') ?></td>
                        <td><strong><?= $tp['cantidad'] ?></strong></td>
                        <td>
                            <span class="estado-badge estado-<?= $tp['estado'] ?>">
                                <?= $estado_labels[$tp['estado']] ?? $tp['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($tp['respuesta_texto'])): ?>
                                <div style="font-size: 0.85rem;"><?= htmlspecialchars($tp['respuesta_texto']) ?></div>
                            <?php elseif (in_array($tp['estado'], ['enviando', 'no_existe', 'stock_agotado'])): ?>
                                <span style="color: var(--gris); font-size: 0.85rem;">Respondido</span>
                            <?php else: ?>
                                <span style="color: var(--gris);">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tp['confirmado'] ?? 0): ?>
                                <span class="badge badge-verde">&#10003; Si</span>
                                <?php if (!empty($tp['fecha_confirmacion'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--gris);"><?= date('d/m/Y H:i', strtotime($tp['fecha_confirmacion'])) ?></div>
                                <?php endif; ?>
                            <?php elseif (in_array($tp['estado'], ['enviando', 'no_existe', 'stock_agotado'])): ?>
                                <span class="badge badge-amarillo">Pendiente</span>
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

<div id="modalRespuesta" class="modal-respuesta">
    <div class="modal-respuesta-content">
        <h3 id="modalTitulo">Responder Pedido</h3>
        <form method="POST" action="<?= APP_URL ?>/public/pedidos/responder">
            <input type="hidden" name="pedido_id" id="inputPedidoId">
            <input type="hidden" name="tipo_respuesta" id="inputTipoRespuesta">
            
            <div class="form-group">
                <label>Nota / Comentario</label>
                <textarea name="respuesta_texto" id="inputRespuestaTexto" rows="3" placeholder="Escribe una nota para el solicitante..." style="width: 100%; padding: 10px; border: 2px solid var(--gris-claro); border-radius: 8px; font-family: inherit; font-size: 0.95rem; resize: vertical;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 16px;">
                <button type="submit" class="btn btn-primary">Enviar Respuesta</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalRespuesta()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalRespuesta(pedidoId, tipoRespuesta, repuestoNombre) {
    var modal = document.getElementById('modalRespuesta');
    var titulo = document.getElementById('modalTitulo');
    var inputPedidoId = document.getElementById('inputPedidoId');
    var inputTipoRespuesta = document.getElementById('inputTipoRespuesta');
    var inputTexto = document.getElementById('inputRespuestaTexto');
    
    inputPedidoId.value = pedidoId;
    inputTipoRespuesta.value = tipoRespuesta;
    
    var textos = {
        'enviando': 'Enviando',
        'no_existe': 'No Existe',
        'stock_agotado': 'Stock Agotado'
    };
    
    titulo.textContent = textos[tipoRespuesta] + ' - ' + repuestoNombre;
    
    if (tipoRespuesta === 'enviando') {
        inputTexto.placeholder = 'Ej: Se envia el repuesto por transporte interno...';
    } else if (tipoRespuesta === 'no_existe') {
        inputTexto.placeholder = 'Ej: Este repuesto no existe en nuestro catalogo...';
    } else if (tipoRespuesta === 'stock_agotado') {
        inputTexto.placeholder = 'Ej: El stock se agoto, se realizara pedido al proveedor...';
    }
    
    inputTexto.value = '';
    modal.classList.add('active');
}

function cerrarModalRespuesta() {
    document.getElementById('modalRespuesta').classList.remove('active');
}

document.getElementById('modalRespuesta').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalRespuesta();
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
