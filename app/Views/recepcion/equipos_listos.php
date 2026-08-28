<?php $titulo = 'Equipos Listos para Entregar'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Equipos Reparados - Listos para Entregar</h2>
    </div>
    
    <?php if (empty($equipos)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay equipos listos para entregar en este momento.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Equipo</th>
                        <th>Falla Reportada</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                        <tr>
                            <td><?= $equipo['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($equipo['cliente_nombre'] . ' ' . $equipo['cliente_ap']) ?></strong>
                                <?php if (!empty($equipo['cliente_dni'])): ?>
                                    <br><small>DNI: <?= htmlspecialchars($equipo['cliente_dni']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($equipo['cliente_tel']) ?></td>
                            <td>
                                <strong><?= ucfirst($equipo['tipo_equipo']) ?></strong><br>
                                <small><?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></small>
                            </td>
                            <td>
                                <small><?= htmlspecialchars(substr($equipo['descripcion_falla'] ?? '', 0, 80)) ?>...</small>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/public/recepcion/formulario-entrega?id=<?= $equipo['id'] ?>" class="btn btn-success btn-sm">✓ Entregar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
