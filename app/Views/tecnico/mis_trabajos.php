<?php $titulo = 'Mis Trabajos'; ob_start(); ?>

<style>
.costo-badge {
    background: #2196F3;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: bold;
    display: inline-block;
    margin-top: 5px;
}
.btn-solicitar {
    background: #FF9800;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85em;
    margin-top: 5px;
    display: inline-block;
}
.btn-solicitar:hover {
    background: #F57C00;
}
.solicitudes-lista {
    max-height: 200px;
    overflow-y: auto;
    margin-top: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
}
.solicitud-item {
    padding: 8px;
    border-bottom: 1px solid #eee;
    font-size: 0.9em;
}
.solicitud-item:last-child {
    border-bottom: none;
}
.alerta-envio {
    background: #E3F2FD;
    border: 2px solid #1565C0;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
}
.alerta-envio h3 {
    color: #1565C0;
    margin-bottom: 10px;
}
.btn-recibir {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}
.btn-recibir:hover {
    background: #45a049;
}
</style>

<?php if (!empty($solicitudes_enviadas)): ?>
<div class="alerta-envio">
    <h3>📦 Componentes Enviados por Almacén - Pendientes de Confirmación</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha Solicitud</th>
                    <th>Cliente</th>
                    <th>Equipo</th>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes_enviadas as $sol): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?></td>
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
                    <td>
                        <form method="POST" action="<?= APP_URL ?>/public/pedidos/confirmar-recibido-solicitud" style="display: inline;" onsubmit="return confirm('¿Confirmas que recibiste este componente?');">
                            <input type="hidden" name="solicitud_id" value="<?= $sol['id'] ?>">
                            <button type="submit" class="btn-recibir">✓ Confirmar Recibido</button>
                        </form>
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
        <h2>Mis Trabajos Asignados</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha Asignación</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Equipo</th>
                    <th>Falla Reportada</th>
                    <th>Estado</th>
                    <th>Costo Reparación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trabajos)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No tienes trabajos asignados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($trabajos as $trabajo): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($trabajo['fecha_asignacion'])) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($trabajo['cliente_nombre'] . ' ' . $trabajo['cliente_ap']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($trabajo['cliente_tel'] ?? '-') ?></td>
                        <td>
                            <strong><?= htmlspecialchars($trabajo['tipo_equipo']) ?></strong><br>
                            <small><?= htmlspecialchars($trabajo['marca'] . ' ' . $trabajo['modelo']) ?></small>
                        </td>
                        <td>
                            <small><?= htmlspecialchars(substr($trabajo['descripcion_falla'] ?? '', 0, 100)) ?></small>
                        </td>
                        <td>
                            <?php
                            $estado_class = 'badge-gris';
                            $estado_texto = $trabajo['estado'];
                            
                            if ($trabajo['estado'] === 'en_reparacion') {
                                $estado_class = 'badge-amarillo';
                                $estado_texto = 'En Reparación';
                            } elseif ($trabajo['estado'] === 'completado') {
                                $estado_class = 'badge-verde';
                                $estado_texto = 'Completado';
                            } elseif ($trabajo['estado'] === 'asignado_sucursal') {
                                $estado_class = 'badge-azul';
                                $estado_texto = 'Asignado';
                            } elseif ($trabajo['estado'] === 'recibido') {
                                $estado_class = 'badge-azul';
                                $estado_texto = 'Recibido';
                            }
                            ?>
                            <span class="badge <?= $estado_class ?>"><?= $estado_texto ?></span>
                        </td>
                        <td>
                            <div class="costo-badge" id="costo-<?= $trabajo['id'] ?>">
                                S/ <?= number_format($trabajo['costo_reparacion'] ?? 0, 2) ?>
                            </div>
                            <?php if ($trabajo['estado'] !== 'completado' && $trabajo['estado'] !== 'asignado_sucursal'): ?>
                                <button class="btn-solicitar" onclick="abrirModalSolicitud(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>')">
                                    + Solicitar Componente
                                </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($trabajo['estado'] === 'asignado_sucursal'): ?>
                                <button onclick="confirmarRecibido(<?= $trabajo['id'] ?>)" class="btn btn-success btn-sm">✓ Recibido</button>
                                <button onclick="abrirModalRechazo(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>')" class="btn btn-danger btn-sm">✗ Rechazar</button>
                            <?php elseif ($trabajo['estado'] === 'recibido'): ?>
                                <button onclick="abrirModalActualizar(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>')" class="btn btn-primary btn-sm">Iniciar Reparación</button>
                            <?php elseif ($trabajo['estado'] !== 'completado'): ?>
                                <button onclick="abrirModalActualizar(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>')" class="btn btn-primary btn-sm">Actualizar</button>
                            <?php else: ?>
                                <span style="color: #999;">Finalizado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalActualizar" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Actualizar Trabajo</h2>
            <button class="modal-close" onclick="cerrarModal()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/tecnico/actualizar-trabajo">
            <input type="hidden" name="equipo_id" id="equipo_id">
            <div class="form-group">
                <label>Equipo</label>
                <input type="text" id="equipo_nombre" readonly>
            </div>
            <div class="form-group">
                <label>Acción *</label>
                <select name="accion" required>
                    <option value="">Seleccionar...</option>
                    <option value="inicio_reparacion">Iniciar Reparación</option>
                    <option value="nota_tecnica">Agregar Nota Técnica</option>
                    <option value="completado">Marcar como Completado</option>
                    <option value="pausado">Pausar Trabajo</option>
                </select>
            </div>
            <div class="form-group">
                <label>Descripción / Observaciones</label>
                <textarea name="descripcion" rows="4" placeholder="Describe el trabajo realizado, repuestos utilizados, etc."></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalRechazo" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Rechazar Trabajo</h2>
            <button class="modal-close" onclick="cerrarModal()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/tecnico/rechazar-trabajo">
            <input type="hidden" name="equipo_id" id="rechazo_equipo_id">
            <div class="form-group">
                <label>Equipo</label>
                <input type="text" id="rechazo_equipo_nombre" readonly>
            </div>
            <div class="form-group">
                <label>Motivo del Rechazo *</label>
                <textarea name="motivo" rows="4" placeholder="Indica el motivo por el cual rechazas este trabajo..." required></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-danger">Rechazar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalSolicitud" class="modal-overlay">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Solicitar Componente</h2>
            <button class="modal-close" onclick="cerrarModalSolicitud()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/tecnico/solicitar-componente">
            <input type="hidden" name="equipo_id" id="solicitud_equipo_id">
            <div class="form-group">
                <label>Equipo</label>
                <input type="text" id="solicitud_equipo_nombre" readonly>
            </div>
            <div class="form-group">
                <label>Repuesto *</label>
                <select name="repuesto_id" id="select_repuesto" required>
                    <option value="">Seleccionar repuesto...</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" id="cantidad_solicitud" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label>Precio Unitario</label>
                    <input type="text" id="precio_unitario_solicitud" readonly>
                </div>
            </div>
            <div class="form-group">
                <label>Total</label>
                <input type="text" id="total_solicitud" readonly style="font-weight: bold; color: #2196F3;">
            </div>
            <div class="form-group">
                <label>Motivo / Observaciones</label>
                <textarea name="motivo" rows="3" placeholder="Describe por qué necesitas este componente..."></textarea>
            </div>
            
            <div id="solicitudes_anteriores" style="display: none;">
                <label style="font-weight: bold; margin-bottom: 10px; display: block;">Solicitudes Anteriores:</label>
                <div class="solicitudes-lista" id="lista_solicitudes"></div>
            </div>
            
            <div style="background: #E3F2FD; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <strong>Costo Total de Reparación:</strong>
                <span id="costo_total_reparacion" style="font-size: 1.3em; color: #1976D2; margin-left: 10px;">S/ 0.00</span>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Solicitar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalSolicitud()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalActualizar(equipoId, equipoNombre) {
    document.getElementById('equipo_id').value = equipoId;
    document.getElementById('equipo_nombre').value = equipoNombre;
    document.getElementById('modalActualizar').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalActualizar').classList.remove('active');
    document.getElementById('modalRechazo').classList.remove('active');
    document.getElementById('modalSolicitud').classList.remove('active');
}

function confirmarRecibido(equipoId) {
    if (confirm('¿Confirmas que has recibido este trabajo?')) {
        fetch('<?= APP_URL ?>/public/tecnico/confirmar-recibido', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'equipo_id=' + equipoId
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error al confirmar');
            }
        });
    }
}

function abrirModalRechazo(equipoId, equipoNombre) {
    document.getElementById('rechazo_equipo_id').value = equipoId;
    document.getElementById('rechazo_equipo_nombre').value = equipoNombre;
    document.getElementById('modalRechazo').classList.add('active');
}

function abrirModalSolicitud(equipoId, equipoNombre) {
    document.getElementById('solicitud_equipo_id').value = equipoId;
    document.getElementById('solicitud_equipo_nombre').value = equipoNombre;
    document.getElementById('modalSolicitud').classList.add('active');
    
    cargarRepuestos();
    cargarCostoEquipo(equipoId);
}

function cerrarModalSolicitud() {
    document.getElementById('modalSolicitud').classList.remove('active');
    document.getElementById('select_repuesto').value = '';
    document.getElementById('cantidad_solicitud').value = '1';
    document.getElementById('precio_unitario_solicitud').value = '';
    document.getElementById('total_solicitud').value = '';
}

function cargarRepuestos() {
    fetch('<?= APP_URL ?>/public/tecnico/obtener-repuestos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('select_repuesto');
            select.innerHTML = '<option value="">Seleccionar repuesto...</option>';
            data.forEach(repuesto => {
                const option = document.createElement('option');
                option.value = repuesto.id;
                option.textContent = `${repuesto.nombre} - ${repuesto.marca || 'Sin marca'} (Stock: ${repuesto.stock}) - S/ ${parseFloat(repuesto.precio_unitario || 0).toFixed(2)}`;
                option.dataset.precio = repuesto.precio_unitario || 0;
                select.appendChild(option);
            });
        });
}

function cargarCostoEquipo(equipoId) {
    fetch(`<?= APP_URL ?>/public/tecnico/obtener-costo-equipo?equipo_id=${equipoId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('costo_total_reparacion').textContent = `S/ ${parseFloat(data.total).toFixed(2)}`;
            
            if (data.solicitudes && data.solicitudes.length > 0) {
                const lista = document.getElementById('lista_solicitudes');
                lista.innerHTML = '';
                data.solicitudes.forEach(sol => {
                    const item = document.createElement('div');
                    item.className = 'solicitud-item';
                    item.innerHTML = `
                        <strong>${sol.repuesto_nombre}</strong> - ${sol.cantidad} x S/ ${parseFloat(sol.precio_unitario).toFixed(2)} = S/ ${parseFloat(sol.total).toFixed(2)}
                        <br><small>${sol.motivo || 'Sin observaciones'}</small>
                    `;
                    lista.appendChild(item);
                });
                document.getElementById('solicitudes_anteriores').style.display = 'block';
            } else {
                document.getElementById('solicitudes_anteriores').style.display = 'none';
            }
        });
}

document.getElementById('select_repuesto').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const precio = parseFloat(option.dataset.precio || 0);
    document.getElementById('precio_unitario_solicitud').value = `S/ ${precio.toFixed(2)}`;
    calcularTotalSolicitud();
});

document.getElementById('cantidad_solicitud').addEventListener('input', calcularTotalSolicitud);

function calcularTotalSolicitud() {
    const precio = parseFloat(document.getElementById('precio_unitario_solicitud').value.replace('S/ ', '') || 0);
    const cantidad = parseInt(document.getElementById('cantidad_solicitud').value || 1);
    const total = precio * cantidad;
    document.getElementById('total_solicitud').value = `S/ ${total.toFixed(2)}`;
}

document.getElementById('modalActualizar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

document.getElementById('modalRechazo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

document.getElementById('modalSolicitud').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalSolicitud();
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
