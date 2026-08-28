<?php $titulo = 'Mis Trabajos'; ob_start(); ?>

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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trabajos)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No tienes trabajos asignados</td>
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

<script>
function abrirModalActualizar(equipoId, equipoNombre) {
    document.getElementById('equipo_id').value = equipoId;
    document.getElementById('equipo_nombre').value = equipoNombre;
    document.getElementById('modalActualizar').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalActualizar').classList.remove('active');
    document.getElementById('modalRechazo').classList.remove('active');
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

document.getElementById('modalActualizar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

document.getElementById('modalRechazo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
