<?php $titulo = 'Historial de Auditoría'; ob_start(); ?>

<style>
.badge-crear { background: #4caf50; color: white; }
.badge-editar { background: #2196f3; color: white; }
.badge-eliminar { background: #f44336; color: white; }
.badge-enviar_pedido { background: #ff9800; color: white; }
.datos-json { font-size: 0.8em; color: #666; max-width: 300px; word-break: break-all; }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count(array_filter($historial, fn($h) => $h['accion'] === 'crear')) ?></div>
        <div class="stat-label">Creaciones</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count(array_filter($historial, fn($h) => $h['accion'] === 'editar')) ?></div>
        <div class="stat-label">Ediciones</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count(array_filter($historial, fn($h) => $h['accion'] === 'enviar_pedido')) ?></div>
        <div class="stat-label">Pedidos Enviados</div>
    </div>
    <div class="stat-card negro">
        <div class="stat-value"><?= count($historial) ?></div>
        <div class="stat-label">Total Registros</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Historial de Actividades</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>Tabla</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">No hay registros de actividad</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($historial as $h): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></td>
                        <td><strong><?= htmlspecialchars($h['nombre'] . ' ' . $h['apellido_paterno']) ?></strong></td>
                        <td>
                            <?php
                            $badge_class = 'badge-' . $h['accion'];
                            $accion_texto = [
                                'crear' => 'Crear',
                                'editar' => 'Editar',
                                'eliminar' => 'Eliminar',
                                'enviar_pedido' => 'Enviar Pedido'
                            ];
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= $accion_texto[$h['accion']] ?? $h['accion'] ?></span>
                        </td>
                        <td><?= htmlspecialchars($h['descripcion']) ?></td>
                        <td><span class="badge badge-gris"><?= htmlspecialchars($h['tabla_afectada']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
