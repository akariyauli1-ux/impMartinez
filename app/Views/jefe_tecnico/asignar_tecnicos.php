<?php $titulo = 'Asignar Técnicos'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Equipos Pendientes de Asignación</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Cliente</th>
                    <th>Problema</th>
                    <th>Fecha Registro</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($equipos)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No hay equipos pendientes</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($equipos as $e): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($e['marca'] . ' ' . $e['modelo']) ?></strong><br>
                                <small><?= htmlspecialchars($e['tipo_equipo']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($e['cliente_nombre'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(substr($e['descripcion_falla'] ?? '', 0, 50)) ?>...</td>
                            <td><?= date('d/m/Y', strtotime($e['fecha_registro'])) ?></td>
                            <td>
                                <button onclick="abrirModalAsignar(<?= $e['id'] ?>, '<?= htmlspecialchars($e['marca'] . ' ' . $e['modelo']) ?>')" class="btn btn-primary btn-sm">Asignar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalAsignar" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Asignar Técnico</h2>
            <button class="modal-close" onclick="cerrarModal()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/jefe-tecnico/guardar-asignacion">
            <input type="hidden" name="equipo_id" id="equipo_id">
            <div class="form-group">
                <label>Equipo</label>
                <input type="text" id="equipo_nombre" readonly>
            </div>
            <div class="form-group">
                <label>Técnico Disponible</label>
                <select name="tecnico_id" required>
                    <option value="">Seleccionar técnico...</option>
                    <?php foreach ($tecnicos as $t): ?>
                        <?php if (($t['trabajos'] ?? 0) < 4): ?>
                            <option value="<?= $t['id'] ?>">
                                <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido_paterno']) ?> 
                                (<?= $t['trabajos'] ?? 0 ?>/4 trabajos)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Asignar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAsignar(equipoId, equipoNombre) {
    document.getElementById('equipo_id').value = equipoId;
    document.getElementById('equipo_nombre').value = equipoNombre;
    document.getElementById('modalAsignar').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalAsignar').classList.remove('active');
}

document.getElementById('modalAsignar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
