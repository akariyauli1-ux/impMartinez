<?php $titulo = 'Administradores de Sucursal'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Lista de Administradores</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Sucursal</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admins)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: var(--gris);">
                            No hay administradores registrados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admins as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido_paterno'] . ' ' . $a['apellido_materno']) ?></strong></td>
                            <td><?= htmlspecialchars($a['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($a['sucursal_nombre'] ?? 'Sin asignar') ?></td>
                            <td><?= htmlspecialchars($a['telefono'] ?? '-') ?></td>
                            <td>
                                <?php if ($a['activo']): ?>
                                    <span class="badge badge-verde">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-rojo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
