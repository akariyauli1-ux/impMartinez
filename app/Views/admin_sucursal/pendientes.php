<?php $titulo = 'Equipos Pendientes'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Equipos Pendientes - Asignar a Sucursal</h2>
        <span class="badge badge-amarillo"><?= count($equipos) ?> equipo(s)</span>
    </div>
    
    <?php if (empty($equipos)): ?>
        <p style="text-align: center; padding: 40px; color: #666;">
            <span style="font-size: 3em;">📦</span><br><br>
            No hay equipos pendientes de asignación a sucursal.<br>
            <small>Todos los equipos han sido asignados correctamente.</small>
        </p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Equipo</th>
                        <th>Falla</th>
                        <th>Fecha</th>
                        <th>Asignar a Sucursal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><strong>ORD-<?= str_pad($equipo['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($equipo['cliente_nombre'] . ' ' . $equipo['cliente_ap']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($equipo['cliente_tel'] ?? '-') ?></td>
                        <td>
                            <strong><?= ucfirst($equipo['tipo_equipo']) ?></strong><br>
                            <small><?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></small>
                        </td>
                        <td>
                            <small><?= htmlspecialchars(substr($equipo['descripcion_falla'] ?? '', 0, 60)) ?>...</small>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></td>
                        <td>
                            <?php if ($equipo['estado'] === 'pendiente_asignacion'): ?>
                                <form method="POST" action="<?= APP_URL ?>/public/admin-sucursal/guardar-asignacion" style="display:flex; gap:8px; align-items:center;">
                                    <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">
                                    <select name="sucursal_destino" required style="padding:6px; border:1px solid #ccc; border-radius:4px; min-width:150px;">
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach ($sucursales as $sucursal): ?>
                                            <option value="<?= $sucursal['id'] ?>" <?= ($sucursal['id'] == $_SESSION['sucursal_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($sucursal['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                            <?php elseif ($equipo['estado'] === 'asignado_sucursal'): ?>
                                <span style="color: #2196F3; font-weight: bold;">✓ Ya asignado a sucursal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($equipo['estado'] === 'pendiente_asignacion'): ?>
                                <input type="text" name="motivo" placeholder="Motivo (opcional)" style="padding:6px; border:1px solid #ccc; border-radius:4px; width:120px;">
                                <button type="submit" class="btn btn-primary btn-sm">Asignar</button>
                                </form>
                            <?php elseif ($equipo['estado'] === 'asignado_sucursal'): ?>
                                <span style="color: #666; font-size: 0.9em;">Esperando asignación a técnico</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
