<?php $titulo = 'Historial de Entregas'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Equipos Entregados</h2>
    </div>
    
    <?php if (empty($entregas)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay equipos entregados aún.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Equipo</th>
                        <th>Fecha Entrega</th>
                        <th>Entregado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entregas as $entrega): ?>
                        <tr>
                            <td><?= $entrega['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($entrega['cliente_nombre'] . ' ' . $entrega['cliente_ap']) ?></strong>
                                <?php if (!empty($entrega['cliente_dni'])): ?>
                                    <br><small>DNI: <?= htmlspecialchars($entrega['cliente_dni']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($entrega['cliente_tel']) ?></td>
                            <td>
                                <strong><?= ucfirst($entrega['tipo_equipo']) ?></strong><br>
                                <small><?= htmlspecialchars($entrega['marca'] . ' ' . $entrega['modelo']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-verde">
                                    <?= date('d/m/Y H:i', strtotime($entrega['fecha_entrega'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($entrega['recepcionista_nombre'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
