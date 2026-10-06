<?php $titulo = 'Gestión de Asistencia'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $resumen['total'] ?? 0 ?></div>
        <div class="stat-label">Total Registros</div>
    </div>
    <div class="stat-card" style="background: #FF9800; color: white;">
        <div class="stat-value"><?= $resumen['pendientes'] ?? 0 ?></div>
        <div class="stat-label">Pendientes</div>
    </div>
    <div class="stat-card" style="background: #4CAF50; color: white;">
        <div class="stat-value"><?= $resumen['aprobados'] ?? 0 ?></div>
        <div class="stat-label">Aprobados</div>
    </div>
    <div class="stat-card" style="background: #f44336; color: white;">
        <div class="stat-value"><?= $resumen['rechazados'] ?? 0 ?></div>
        <div class="stat-label">Rechazados</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Filtrar por Fecha</h2>
    </div>
    <form method="GET" action="<?= APP_URL ?>/public/gestion-asistencia" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Fecha</label>
            <input type="date" name="fecha" value="<?= $fecha ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>

<?php if (!empty($pendientes)): ?>
<div class="card">
    <div class="card-header">
        <h2>Asistencias Pendientes de Verificación</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Carnet</th>
                    <th>Rol</th>
                    <th>Fecha</th>
                    <th>Hora Entrada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendientes as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['nombre_completo']) ?></strong></td>
                        <td><?= htmlspecialchars($p['carnet']) ?></td>
                        <td><span class="badge badge-gris"><?= ucfirst(str_replace('_', ' ', $p['rol_nombre'] ?? 'N/A')) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                        <td><?= $p['hora_entrada'] ?? '-' ?></td>
                        <td>
                            <?php
                            $estado_class = 'badge-amarillo';
                            $estado_texto = 'Pendiente';
                            if ($p['estado'] === 'tardanza') {
                                $estado_texto = 'Tardanza';
                            } elseif ($p['estado'] === 'presente') {
                                $estado_class = 'badge-verde';
                                $estado_texto = 'Presente';
                            }
                            ?>
                            <span class="badge <?= $estado_class ?>"><?= $estado_texto ?></span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <form method="POST" action="<?= APP_URL ?>/public/gestion-asistencia/aprobar" style="margin: 0;">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Aprobar esta asistencia?')">✓ Aprobar</button>
                                </form>
                                <button onclick="mostrarModalRechazo(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre_completo']) ?>')" class="btn btn-danger btn-sm">✗ Rechazar</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($historial)): ?>
<div class="card">
    <div class="card-header">
        <h2>Historial de Asistencias Verificadas</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Carnet</th>
                    <th>Rol</th>
                    <th>Fecha</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Estado</th>
                    <th>Verificación</th>
                    <th>Aprobado Por</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $h): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($h['nombre_completo']) ?></strong></td>
                        <td><?= htmlspecialchars($h['carnet']) ?></td>
                        <td><span class="badge badge-gris"><?= ucfirst(str_replace('_', ' ', $h['rol_nombre'] ?? 'N/A')) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                        <td><?= $h['hora_entrada'] ?? '-' ?></td>
                        <td><?= $h['hora_salida'] ?? '-' ?></td>
                        <td>
                            <?php
                            $estado_class = 'badge-gris';
                            $estado_texto = ucfirst($h['estado']);
                            if ($h['estado'] === 'presente') {
                                $estado_class = 'badge-verde';
                            } elseif ($h['estado'] === 'tardanza') {
                                $estado_class = 'badge-amarillo';
                            } elseif ($h['estado'] === 'ausente') {
                                $estado_class = 'badge-rojo';
                            } elseif ($h['estado'] === 'permiso') {
                                $estado_class = 'badge-azul';
                            }
                            ?>
                            <span class="badge <?= $estado_class ?>"><?= $estado_texto ?></span>
                        </td>
                        <td>
                            <?php if ($h['estado_asistencia'] === 'aprobado'): ?>
                                <span class="badge badge-verde">✓ Aprobado</span>
                            <?php elseif ($h['estado_asistencia'] === 'rechazado'): ?>
                                <span class="badge badge-rojo">✗ Rechazado</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($h['aprobado_por_nombre'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (empty($pendientes) && empty($historial)): ?>
<div class="card">
    <div class="card-header">
        <h2>Sin Registros</h2>
    </div>
    <div style="text-align: center; padding: 40px; color: #666;">
        <p style="font-size: 48px; margin-bottom: 20px;">📋</p>
        <p>No hay registros de asistencia para esta fecha</p>
    </div>
</div>
<?php endif; ?>

<!-- Modal de Rechazo -->
<div id="modalRechazo" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h2>Rechazar Asistencia</h2>
            <button class="modal-close" onclick="cerrarModalRechazo()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/gestion-asistencia/rechazar">
            <input type="hidden" name="id" id="rechazo_id">
            <div class="form-group">
                <label>Empleado</label>
                <input type="text" id="rechazo_empleado" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Motivo del Rechazo</label>
                <textarea name="observaciones" class="form-control" rows="3" placeholder="Indique el motivo del rechazo..." required></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="cerrarModalRechazo()" class="btn btn-outline">Cancelar</button>
                <button type="submit" class="btn btn-danger">Rechazar Asistencia</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
<div id="toast-exito" style="position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 9999; display: flex; align-items: center; gap: 10px;">
    <span style="font-size: 1.5em;">✓</span>
    <div>
        <strong><?= $_SESSION['mensaje_exito'] ?></strong>
    </div>
</div>
<?php unset($_SESSION['mensaje_exito']); ?>
<script>
    setTimeout(() => {
        const toast = document.getElementById('toast-exito');
        if (toast) toast.remove();
    }, 3000);
</script>
<?php endif; ?>

<script>
function mostrarModalRechazo(id, empleado) {
    document.getElementById('rechazo_id').value = id;
    document.getElementById('rechazo_empleado').value = empleado;
    document.getElementById('modalRechazo').style.display = 'flex';
}

function cerrarModalRechazo() {
    document.getElementById('modalRechazo').style.display = 'none';
}

document.getElementById('modalRechazo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalRechazo();
});
</script>


<?php $css_extra = 'gestion_asistencia.css'; $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
