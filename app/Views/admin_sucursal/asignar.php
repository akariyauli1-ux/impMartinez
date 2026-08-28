<?php $titulo = 'Asignar Equipos a Sucursal'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Asignar Equipos a Sucursal</h2>
    </div>
    
    <?php if (empty($equipos)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay equipos pendientes de asignación a sucursal.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Marca/Modelo</th>
                    <th>Fecha Registro</th>
                    <th>Asignar a</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><?= $equipo['id'] ?></td>
                        <td><?= htmlspecialchars($equipo['cliente_nombre'] . ' ' . $equipo['cliente_ap']) ?></td>
                        <td><?= ucfirst($equipo['tipo_equipo']) ?></td>
                        <td><?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></td>
                        <td>
                            <form method="POST" action="<?= APP_URL ?>/public/admin-sucursal/guardar-asignacion" style="display:flex; gap:8px; align-items:center;">
                                <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">
                                <select name="sucursal_destino" required style="padding:6px; border:1px solid #ccc; border-radius:4px;">
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($sucursales as $sucursal): ?>
                                        <option value="<?= $sucursal['id'] ?>" <?= ($sucursal['id'] == $_SESSION['sucursal_id']) ? 'selected' : '' ?>><?= htmlspecialchars($sucursal['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="motivo" placeholder="Motivo" style="padding:6px; border:1px solid #ccc; border-radius:4px; width:150px;">
                        </td>
                        <td>
                                <button type="submit" class="btn btn-primary btn-sm">Asignar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
