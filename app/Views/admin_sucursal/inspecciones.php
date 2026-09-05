<?php $titulo = 'Inspecciones de Limpieza y Uniforme'; ob_start(); ?>

<style>
@media (max-width: 768px) {
    .table-responsive-mobile {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table-mobile {
        border: 0;
    }
    
    .table-mobile thead {
        display: none;
    }
    
    .table-mobile tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        background: #f9f9f9;
    }
    
    .table-mobile td {
        display: block;
        text-align: left;
        padding: 8px;
        border: none;
        position: relative;
        padding-left: 50%;
    }
    
    .table-mobile td:before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        width: 45%;
        font-weight: bold;
        white-space: nowrap;
    }
    
    .table-mobile input[type="text"],
    .table-mobile input[type="time"],
    .table-mobile input[type="date"] {
        width: 100%;
        padding: 8px;
        font-size: 16px;
    }
    
    .table-mobile input[type="checkbox"] {
        width: 20px;
        height: 20px;
    }
    
    .form-mobile {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-mobile label {
        margin-bottom: 5px;
    }
    
    .form-mobile input,
    .form-mobile button {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .btn-mobile {
        width: 100%;
        padding: 12px;
        font-size: 16px;
    }
}
</style>

<div class="card">
    <div class="card-header">
        <h2>Inspecciones de Limpieza y Uniforme</h2>
    </div>
    
    <form method="GET" action="<?= APP_URL ?>/public/admin-sucursal/inspecciones" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;" class="form-mobile">
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
            
            <div class="table-responsive-mobile">
            <table class="table table-mobile">
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
                            <td data-label="Empleado"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido_paterno']) ?></td>
                            <td data-label="Rol"><?= ucfirst(str_replace('_', ' ', $p['rol'])) ?></td>
                            <td data-label="Limpieza">
                                <label style="display:flex; align-items:center; gap:5px;">
                                    <input type="checkbox" name="inspecciones[<?= $p['usuario_id'] ?>][limpieza_check]" <?= ($p['limpieza'] ?? '') === 'aprobado' ? 'checked' : '' ?> onchange="setHoraActual(this, 'inspecciones[<?= $p['usuario_id'] ?>][hora_limpieza]')">
                                    <span>Aprobado</span>
                                </label>
                            </td>
                            <td data-label="Hora Limpieza">
                                <input type="time" name="inspecciones[<?= $p['usuario_id'] ?>][hora_limpieza]" value="<?= htmlspecialchars($p['hora_revision_limpieza'] ?? '') ?>" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                            <td data-label="Obs. Limpieza">
                                <input type="text" name="inspecciones[<?= $p['usuario_id'] ?>][obs_limpieza]" value="<?= htmlspecialchars($p['obs_limpieza'] ?? '') ?>" placeholder="Observaciones" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                            </td>
                            <td data-label="Uniforme">
                                <label style="display:flex; align-items:center; gap:5px;">
                                    <input type="checkbox" name="inspecciones[<?= $p['usuario_id'] ?>][uniforme_check]" <?= ($p['uniforme'] ?? '') === 'completo' ? 'checked' : '' ?> onchange="setHoraActual(this, 'inspecciones[<?= $p['usuario_id'] ?>][hora_uniforme]')">
                                    <span>Completo</span>
                                </label>
                            </td>
                            <td data-label="Hora Uniforme">
                                <input type="time" name="inspecciones[<?= $p['usuario_id'] ?>][hora_uniforme]" value="<?= htmlspecialchars($p['hora_revision_uniforme'] ?? '') ?>" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                            <td data-label="Obs. Uniforme">
                                <input type="text" name="inspecciones[<?= $p['usuario_id'] ?>][obs_uniforme]" value="<?= htmlspecialchars($p['obs_uniforme'] ?? '') ?>" placeholder="Observaciones" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary btn-mobile">Guardar Inspecciones</button>
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
        <div class="table-container table-responsive-mobile">
            <table class="table table-mobile">
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
                            <td data-label="Fecha"><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                            <td data-label="Empleado"><?= htmlspecialchars($h['nombre'] . ' ' . $h['apellido_paterno']) ?></td>
                            <td data-label="Rol"><?= ucfirst(str_replace('_', ' ', $h['rol'])) ?></td>
                            <td data-label="Limpieza">
                                <?php if ($h['limpieza'] === 'aprobado'): ?>
                                    <span class="badge badge-verde">Aprobado</span>
                                <?php elseif ($h['limpieza'] === 'observado'): ?>
                                    <span class="badge badge-amarillo">Observado</span>
                                <?php else: ?>
                                    <span class="badge badge-rojo">Rechazado</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Hora Limpieza"><?= $h['hora_revision_limpieza'] ? date('H:i', strtotime($h['hora_revision_limpieza'])) : '-' ?></td>
                            <td data-label="Obs. Limpieza"><?= htmlspecialchars($h['obs_limpieza'] ?? '-') ?></td>
                            <td data-label="Uniforme">
                                <?php if ($h['uniforme'] === 'completo'): ?>
                                    <span class="badge badge-verde">Completo</span>
                                <?php elseif ($h['uniforme'] === 'observado'): ?>
                                    <span class="badge badge-amarillo">Observado</span>
                                <?php else: ?>
                                    <span class="badge badge-rojo">Incompleto</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Hora Uniforme"><?= $h['hora_revision_uniforme'] ? date('H:i', strtotime($h['hora_revision_uniforme'])) : '-' ?></td>
                            <td data-label="Obs. Uniforme"><?= htmlspecialchars($h['obs_uniforme'] ?? '-') ?></td>
                            <td data-label="Registrado por"><?= htmlspecialchars($h['registrado_por_nombre'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
