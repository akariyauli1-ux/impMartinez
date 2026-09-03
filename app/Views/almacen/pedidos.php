<?php $titulo = 'Pedidos de Repuestos'; ob_start(); ?>

<?php if (!empty($_SESSION['error_pedido'])): ?>
<div class="alert alert-error" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #c62828; font-weight: bold; font-size: 1.1em;">
    ⚠️ <?= htmlspecialchars($_SESSION['error_pedido']) ?>
    <?php unset($_SESSION['error_pedido']); ?>
</div>
<?php endif; ?>

<style>
.solicitudes-tecnicos {
    background: #FFF3E0;
    border: 2px solid #FF9800;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}
.solicitudes-tecnicos h2 {
    color: #E65100;
    margin-bottom: 15px;
}
.badge-solicitado {
    background: #FF9800;
    color: white;
}
.badge-entregado {
    background: #4CAF50;
    color: white;
}
.btn-entregar {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85em;
}
.btn-entregar:hover {
    background: #45a049;
}
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($solicitudes ?? []) ?></div>
        <div class="stat-label">Solicitudes Técnicos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($pedidos ?? []) ?></div>
        <div class="stat-label">Pedidos Almacén</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($repuestos ?? []) ?></div>
        <div class="stat-label">Repuestos Disponibles</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($tecnicos ?? []) ?></div>
        <div class="stat-label">Tecnicos</div>
    </div>
</div>

<?php if (!empty($solicitudes)): ?>
<div class="solicitudes-tecnicos">
    <h2>🔧 Solicitudes de Componentes de Técnicos</h2>
    <p style="margin-bottom: 15px; color: #666;">Los técnicos han solicitado estos componentes para reparaciones en curso</p>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Técnico</th>
                    <th>Cliente</th>
                    <th>Equipo</th>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Total</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $sol): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?></td>
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
                    <td><?= $sol['cantidad'] ?></td>
                    <td>S/ <?= number_format($sol['precio_unitario'], 2) ?></td>
                    <td><strong>S/ <?= number_format($sol['total'], 2) ?></strong></td>
                    <td><small><?= htmlspecialchars($sol['motivo'] ?? 'Sin observaciones') ?></small></td>
                    <td>
                        <?php 
                        $estado_class = 'badge-gris';
                        $estado_texto = $sol['estado'];
                        if ($sol['estado'] === 'solicitado') {
                            $estado_class = 'badge-solicitado';
                            $estado_texto = 'Solicitado';
                        } elseif ($sol['estado'] === 'entregado') {
                            $estado_class = 'badge-entregado';
                            $estado_texto = 'Entregado';
                        }
                        ?>
                        <span class="badge <?= $estado_class ?>"><?= $estado_texto ?></span>
                    </td>
                    <td>
                        <?php if ($sol['estado'] === 'solicitado'): ?>
                            <form method="POST" action="<?= APP_URL ?>/public/almacen/entregar-solicitud" style="display: inline;" onsubmit="return confirm('¿Confirmas la entrega de este componente?');">
                                <input type="hidden" name="solicitud_id" value="<?= $sol['id'] ?>">
                                <button type="submit" class="btn-entregar">✓ Entregar</button>
                            </form>
                        <?php else: ?>
                            <span style="color: #999;">Completado</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Pedidos de Almacén</h2>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modalNuevo').classList.add('active')">+ Nuevo Pedido</button>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Repuesto</th>
                    <th>Tecnico</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Total</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidos)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No hay pedidos registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td><?= $p['fecha_solicitud'] ?? '' ?></td>
                        <td>
                            <strong><?= htmlspecialchars($p['repuesto_nombre'] ?? '') ?></strong>
                            <br><small><?= htmlspecialchars($p['repuesto_codigo'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars(($p['tecnico_nombre'] ?? '') . ' ' . ($p['tecnico_apellido'] ?? '')) ?></td>
                        <td><?= $p['cantidad'] ?? 0 ?></td>
                        <td>S/ <?= number_format($p['precio_unitario'] ?? 0, 2) ?></td>
                        <td><strong>S/ <?= number_format($p['total'] ?? 0, 2) ?></strong></td>
                        <td><?= htmlspecialchars($p['sucursal_nombre'] ?? '') ?></td>
                        <td>
                            <?php 
                            $estado = $p['estado'] ?? 'solicitado';
                            $badge_class = 'badge-gris';
                            if ($estado === 'aprobado') $badge_class = 'badge-verde';
                            elseif ($estado === 'enviado') $badge_class = 'badge-amarillo';
                            elseif ($estado === 'recibido') $badge_class = 'badge-negro';
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= ucfirst($estado) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalNuevo" class="modal-overlay">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <h2>Nuevo Pedido</h2>
            <button class="modal-close" onclick="cerrarModal()">x</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/almacen/guardar-pedido">
            <div class="form-group">
                <label>Repuesto *</label>
                <select name="repuesto_id" id="selectRepuesto" required onchange="actualizarPrecio()">
                    <option value="">Seleccionar repuesto...</option>
                    <?php if (!empty($repuestos)): ?>
                        <?php foreach ($repuestos as $r): ?>
                        <option value="<?= $r['id'] ?>" data-precio="<?= $r['precio_unitario'] ?>" data-stock="<?= $r['unidades_disponibles'] ?? 0 ?>">
                            <?= htmlspecialchars($r['nombre']) ?> - <?= htmlspecialchars($r['marca'] ?? '') ?> (Disp: <?= $r['unidades_disponibles'] ?? 0 ?>)
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Tecnico *</label>
                    <select name="tecnico_id" required>
                        <option value="">Seleccionar tecnico...</option>
                        <?php if (!empty($tecnicos)): ?>
                            <?php foreach ($tecnicos as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido_paterno']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sucursal *</label>
                    <select name="sucursal_id" required>
                        <?php if (!empty($sucursales)): ?>
                            <?php foreach ($sucursales as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $s['id'] == ($_SESSION['sucursal_id'] ?? '') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" id="inputCantidad" min="1" value="1" required onchange="calcularTotal()">
                </div>
                <div class="form-group">
                    <label>Precio Unitario (S/)</label>
                    <input type="text" id="precioUnitario" readonly value="0.00">
                </div>
            </div>
            <div class="form-group">
                <label>Total (S/)</label>
                <input type="text" id="totalPedido" readonly value="0.00" style="font-size: 1.2em; font-weight: bold; color: var(--primario);">
            </div>
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="btn btn-primary">Registrar Pedido</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function actualizarPrecio() {
    var select = document.getElementById('selectRepuesto');
    var option = select.options[select.selectedIndex];
    var precio = option.getAttribute('data-precio') || 0;
    var stock = parseInt(option.getAttribute('data-stock')) || 0;
    document.getElementById('precioUnitario').value = parseFloat(precio).toFixed(2);
    
    var inputCantidad = document.getElementById('inputCantidad');
    if (stock <= 0) {
        alert('⚠️ YA NO HAY DISPONIBLE EN ALMACEN para este repuesto');
        inputCantidad.max = 0;
        inputCantidad.value = 0;
        inputCantidad.disabled = true;
    } else {
        inputCantidad.max = stock;
        inputCantidad.disabled = false;
        if (parseInt(inputCantidad.value) > stock) {
            inputCantidad.value = stock;
        }
    }
    calcularTotal();
}

function calcularTotal() {
    var precio = parseFloat(document.getElementById('precioUnitario').value) || 0;
    var cantidad = parseInt(document.getElementById('inputCantidad').value) || 0;
    var total = precio * cantidad;
    document.getElementById('totalPedido').value = total.toFixed(2);
}

function cerrarModal() {
    document.getElementById('modalNuevo').classList.remove('active');
    document.getElementById('selectRepuesto').value = '';
    document.getElementById('inputCantidad').value = 1;
    document.getElementById('inputCantidad').disabled = false;
    document.getElementById('precioUnitario').value = '0.00';
    document.getElementById('totalPedido').value = '0.00';
}

document.getElementById('modalNuevo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

actualizarPrecio();
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
