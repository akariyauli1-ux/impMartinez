<?php $titulo = 'Control de Entregas'; ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($completados) ?></div>
        <div class="stat-label">Equipos Listos para Entregar</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= count($entregados) ?></div>
        <div class="stat-label">Equipos Entregados</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Equipos Pendientes de Entrega</h2>
    </div>
    
    <?php if (empty($completados)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay equipos pendientes de entrega.</p>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Equipo</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completados as $equipo): ?>
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
                            <td><?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></td>
                            <td>
                                <span class="badge badge-amarillo">Pendiente de entrega</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2>Historial de Entregas Realizadas</h2>
    </div>
    
    <?php if (empty($entregados)): ?>
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
                    <?php foreach ($entregados as $entrega): ?>
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
