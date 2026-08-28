<?php $titulo = 'Equipos Pendientes'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Equipos Pendientes de Asignación a Técnico</h2>
    </div>
    
    <?php if (empty($pendientes)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay equipos pendientes de asignación.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Marca/Modelo</th>
                    <th>Falla</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendientes as $equipo): ?>
                    <tr>
                        <td><?= $equipo['id'] ?></td>
                        <td><?= htmlspecialchars($equipo['cliente_nombre'] . ' ' . $equipo['cliente_ap']) ?></td>
                        <td><?= ucfirst($equipo['tipo_equipo']) ?></td>
                        <td><?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></td>
                        <td><?= htmlspecialchars(substr($equipo['descripcion_falla'], 0, 50)) ?>...</td>
                        <td><?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></td>
                        <td>
                            <a href="<?= APP_URL ?>/public/admin-sucursal/asignar" class="btn btn-primary btn-sm">Asignar Técnico</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
