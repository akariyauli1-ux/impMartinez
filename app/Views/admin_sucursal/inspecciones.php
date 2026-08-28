<?php $titulo = 'Inspecciones de Limpieza y Uniforme'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Inspecciones de Limpieza y Uniforme</h2>
    </div>
    
    <form method="GET" action="<?= APP_URL ?>/public/admin-sucursal/inspecciones" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($fecha) ?>" required style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <button type="submit" class="btn btn-primary">Consultar</button>
        </div>
    </form>
    
    <?php if (empty($personal)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay personal registrado para inspeccionar en esta fecha.</p>
    <?php else: ?>
        <form method="POST" action="<?= APP_URL ?>/public/admin-sucursal/guardar-inspecciones">
            <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Rol</th>
                        <th>Limpieza</th>
                        <th>Hora Limpieza</th>
                        <th>Obs. Limpieza</th>
                        <th>Uniforme</th>
                        <th>Hora Uniforme</th>
                        <th>Obs. Uniforme</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personal as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido_paterno']) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $p['rol'])) ?></td>
                            <td>
                                <label style="display:flex; align-items:center; gap:5px;">
                                    <input type="checkbox" name="inspecciones[<?= $p['usuario_id'] ?>][limpieza_check]" <?= ($p['limpieza'] ?? '') === 'aprobado' ? 'checked' : '' ?> onchange="setHoraActual(this, 'inspecciones[<?= $p['usuario_id'] ?>][hora_limpieza]')">
                                    <span>Aprobado</span>
                                </label>
                            </td>
                            <td>
                                <input type="time" name="inspecciones[<?= $p['usuario_id'] ?>][hora_limpieza]" value="<?= htmlspecialchars($p['hora_revision_limpieza'] ?? '') ?>" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                            <td>
                                <input type="text" name="inspecciones[<?= $p['usuario_id'] ?>][obs_limpieza]" value="<?= htmlspecialchars($p['obs_limpieza'] ?? '') ?>" placeholder="Observaciones" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                            </td>
                            <td>
                                <label style="display:flex; align-items:center; gap:5px;">
                                    <input type="checkbox" name="inspecciones[<?= $p['usuario_id'] ?>][uniforme_check]" <?= ($p['uniforme'] ?? '') === 'completo' ? 'checked' : '' ?> onchange="setHoraActual(this, 'inspecciones[<?= $p['usuario_id'] ?>][hora_uniforme]')">
                                    <span>Completo</span>
                                </label>
                            </td>
                            <td>
                                <input type="time" name="inspecciones[<?= $p['usuario_id'] ?>][hora_uniforme]" value="<?= htmlspecialchars($p['hora_revision_uniforme'] ?? '') ?>" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                            <td>
                                <input type="text" name="inspecciones[<?= $p['usuario_id'] ?>][obs_uniforme]" value="<?= htmlspecialchars($p['obs_uniforme'] ?? '') ?>" placeholder="Observaciones" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary">Guardar Inspecciones</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function setHoraActual(checkbox, inputName) {
    const input = document.querySelector(`input[name="${inputName}"]`);
    if (checkbox.checked) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        input.value = `${hours}:${minutes}`;
    }
}
</script>

<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2>Historial de Inspecciones</h2>
    </div>
    
    <?php if (empty($historial)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay inspecciones registradas aún.</p>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th>Rol</th>
                        <th>Limpieza</th>
                        <th>Hora Limpieza</th>
                        <th>Obs. Limpieza</th>
                        <th>Uniforme</th>
                        <th>Hora Uniforme</th>
                        <th>Obs. Uniforme</th>
                        <th>Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                            <td><?= htmlspecialchars($h['nombre'] . ' ' . $h['apellido_paterno']) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $h['rol'])) ?></td>
                            <td>
                                <?php if ($h['limpieza'] === 'aprobado'): ?>
                                    <span class="badge badge-verde">Aprobado</span>
                                <?php elseif ($h['limpieza'] === 'observado'): ?>
                                    <span class="badge badge-amarillo">Observado</span>
                                <?php else: ?>
                                    <span class="badge badge-rojo">Rechazado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $h['hora_revision_limpieza'] ? date('H:i', strtotime($h['hora_revision_limpieza'])) : '-' ?></td>
                            <td><?= htmlspecialchars($h['obs_limpieza'] ?? '-') ?></td>
                            <td>
                                <?php if ($h['uniforme'] === 'completo'): ?>
                                    <span class="badge badge-verde">Completo</span>
                                <?php elseif ($h['uniforme'] === 'observado'): ?>
                                    <span class="badge badge-amarillo">Observado</span>
                                <?php else: ?>
                                    <span class="badge badge-rojo">Incompleto</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $h['hora_revision_uniforme'] ? date('H:i', strtotime($h['hora_revision_uniforme'])) : '-' ?></td>
                            <td><?= htmlspecialchars($h['obs_uniforme'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($h['registrado_por_nombre'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
