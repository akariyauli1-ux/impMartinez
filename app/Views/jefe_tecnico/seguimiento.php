<?php $titulo = 'Seguimiento de Trabajos'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Trabajos Asignados</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Técnico</th>
                    <th>Fecha Asignación</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asignaciones)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">No hay trabajos asignados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($asignaciones as $a): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($a['equipo_tipo'] . ' - ' . $a['equipo_marca'] . ' ' . $a['equipo_modelo']) ?></strong><br>
                                <small><?= htmlspecialchars($a['cliente_nombre'] . ' ' . $a['cliente_apellido']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($a['tecnico_nombre'] . ' ' . $a['tecnico_apellido']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['fecha_asignacion'])) ?></td>
                            <td>
                                <?php if ($a['equipo_estado'] === 'completado'): ?>
                                    <span class="badge badge-verde">Completado</span>
                                <?php elseif ($a['equipo_estado'] === 'en_reparacion'): ?>
                                    <span class="badge badge-amarillo">En Reparación</span>
                                <?php else: ?>
                                    <span class="badge badge-gris"><?= ucfirst($a['equipo_estado']) ?></span>
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
