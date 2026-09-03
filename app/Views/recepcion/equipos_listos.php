<?php $titulo = 'Equipos Listos para Entregar'; ob_start(); ?>

<style>
.componentes-lista {
    margin-top: 8px;
    padding: 8px;
    background: #f9f9f9;
    border-radius: 4px;
    font-size: 0.8rem;
}
.componente-item {
    padding: 4px 0;
    border-bottom: 1px dashed #ddd;
}
.componente-item:last-child {
    border-bottom: none;
}
.costo-desglose {
    margin-top: 8px;
    padding: 8px;
    background: #E3F2FD;
    border-radius: 4px;
    font-size: 0.8rem;
}
.costo-desglose .linea {
    display: flex;
    justify-content: space-between;
    padding: 2px 0;
}
.costo-desglose .total {
    border-top: 2px solid #1565C0;
    margin-top: 4px;
    padding-top: 4px;
    font-weight: bold;
    color: #1565C0;
}
.btn-entregar {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
}
.btn-entregar:hover {
    background: #45a049;
}
</style>

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
                        <th>Fecha Estimada</th>
                        <th>Costo Estimado</th>
                        <th>Componentes Usados</th>
                        <th>Costo Reparación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                        <?php 
                        $costo_estimado = $equipo['costo_estimado'] ?? 0;
                        $costo_reparacion = $equipo['costo_reparacion'] ?? 0;
                        $tiene_componentes = !empty($equipo['componentes']);
                        ?>
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
                                <?php if (!empty($equipo['fecha_estimada_entrega'])): ?>
                                    <strong><?= date('d/m/Y', strtotime($equipo['fecha_estimada_entrega'])) ?></strong>
                                <?php else: ?>
                                    <span style="color: #999;">No definida</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: #666;">S/ <?= number_format($costo_estimado, 2) ?></strong>
                            </td>
                            <td>
                                <?php if ($tiene_componentes): ?>
                                    <div class="componentes-lista">
                                        <?php foreach ($equipo['componentes'] as $comp): ?>
                                            <div class="componente-item">
                                                <strong><?= htmlspecialchars($comp['repuesto_nombre']) ?></strong>
                                                <br><small><?= $comp['cantidad'] ?> x S/ <?= number_format($comp['precio_unitario'], 2) ?> = <strong>S/ <?= number_format($comp['total'], 2) ?></strong></small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 0.85rem;">Sin componentes</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="costo-desglose">
                                    <div class="linea">
                                        <span>Componentes:</span>
                                        <span>S/ <?= number_format($costo_reparacion, 2) ?></span>
                                    </div>
                                    <?php if ($costo_estimado > 0 && $costo_estimado != $costo_reparacion): ?>
                                        <div class="linea" style="color: <?= $costo_reparacion > $costo_estimado ? '#C62828' : '#2E7D32' ?>;">
                                            <span>Diferencia:</span>
                                            <span><?= $costo_reparacion > $costo_estimado ? '+' : '' ?>S/ <?= number_format($costo_reparacion - $costo_estimado, 2) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="linea total">
                                        <span>Total:</span>
                                        <span>S/ <?= number_format($costo_reparacion, 2) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="<?= APP_URL ?>/public/recepcion/formulario-entrega?id=<?= $equipo['id'] ?>" class="btn-entregar">✓ Entregar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
