<?php $titulo = 'Mis Registros'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Equipos Registrados por Mí</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Equipo</th>
                    <th>Falla</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No has registrado equipos aún</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($registro['fecha_registro'])) ?></td>
                            <td><?= htmlspecialchars($registro['cliente_nombre'] . ' ' . $registro['cliente_ap']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($registro['tipo_equipo']) ?></strong><br>
                                <small><?= htmlspecialchars($registro['marca'] . ' ' . $registro['modelo']) ?></small>
                            </td>
                            <td><?= htmlspecialchars(substr($registro['descripcion_falla'], 0, 50)) ?>...</td>
                            <td>
                                <?php
                                $estado_class = 'badge-gris';
                                $estado_texto = $registro['estado'];
                                
                                if ($registro['estado'] === 'registrado') {
                                    $estado_class = 'badge-azul';
                                    $estado_texto = 'Registrado';
                                } elseif ($registro['estado'] === 'pendiente_asignacion') {
                                    $estado_class = 'badge-amarillo';
                                    $estado_texto = 'Pendiente';
                                } elseif ($registro['estado'] === 'en_reparacion') {
                                    $estado_class = 'badge-naranja';
                                    $estado_texto = 'En Reparación';
                                } elseif ($registro['estado'] === 'completado') {
                                    $estado_class = 'badge-verde';
                                    $estado_texto = 'Completado';
                                } elseif ($registro['estado'] === 'entregado') {
                                    $estado_class = 'badge-verde';
                                    $estado_texto = 'Entregado';
                                }
                                ?>
                                <span class="badge <?= $estado_class ?>"><?= $estado_texto ?></span>
                            </td>
                            <td>
                                <a href="<?= APP_URL ?>/public/recepcion/ver-recibo?id=<?= $registro['id'] ?>" 
                                   class="btn btn-primary btn-sm" 
                                   target="_blank">
                                    📄 Ver Recibo
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
