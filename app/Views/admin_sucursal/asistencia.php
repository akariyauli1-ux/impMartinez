<?php $titulo = 'Control de Asistencia'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Control de Asistencia</h2>
    </div>
    
    <form method="GET" action="<?= APP_URL ?>/public/admin-sucursal/asistencia" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($fecha) ?>" required style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <button type="submit" class="btn btn-primary">Consultar</button>
        </div>
    </form>
    
    <?php if (empty($personal)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay personal registrado para esta fecha.</p>
    <?php else: ?>
        <form method="POST" action="<?= APP_URL ?>/public/admin-sucursal/guardar-asistencia">
            <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Rol</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personal as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido_paterno']) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $p['rol'])) ?></td>
                            <td>
                                <input type="time" name="empleados[<?= $p['usuario_id'] ?>][hora_entrada]" value="<?= htmlspecialchars($p['hora_entrada'] ?? '') ?>" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                            <td>
                                <input type="time" name="empleados[<?= $p['usuario_id'] ?>][hora_salida]" value="<?= htmlspecialchars($p['hora_salida'] ?? '') ?>" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                            <td>
                                <select name="empleados[<?= $p['usuario_id'] ?>][estado]" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px;">
                                    <option value="presente" <?= ($p['estado'] ?? '') == 'presente' ? 'selected' : '' ?>>Presente</option>
                                    <option value="tardanza" <?= ($p['estado'] ?? '') == 'tardanza' ? 'selected' : '' ?>>Tardanza</option>
                                    <option value="ausente" <?= ($p['estado'] ?? '') == 'ausente' ? 'selected' : '' ?>>Ausente</option>
                                    <option value="permiso" <?= ($p['estado'] ?? '') == 'permiso' ? 'selected' : '' ?>>Permiso</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="empleados[<?= $p['usuario_id'] ?>][observaciones]" value="<?= htmlspecialchars($p['observaciones'] ?? '') ?>" placeholder="Observaciones" style="padding: 4px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary">Guardar Asistencia</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
