<?php $titulo = 'Mis Trabajos'; ob_start(); ?>

<?php if (!empty($_SESSION['error_solicitud'])): ?>
<div style="background: #FFEBEE; border: 2px solid #C62828; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
    <strong style="color: #C62828;">⚠️ <?= htmlspecialchars($_SESSION['error_solicitud']) ?></strong>
    <?php unset($_SESSION['error_solicitud']); ?>
</div>
<?php endif; ?>

<?php if ($componentes_pendientes > 0): ?>
<div style="background: #FFF3E0; border: 2px solid #FF9800; border-radius: 8px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
    <div style="background: #FF9800; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5em; font-weight: bold;">
        <?= $componentes_pendientes ?>
    </div>
    <div>
        <strong style="color: #E65100; font-size: 1.1em;">📦 Esperando <?= $componentes_pendientes ?> componente(s) de Almacén</strong>
        <p style="color: #BF360C; margin: 5px 0 0 0; font-size: 0.9em;">Tienes solicitudes pendientes de componentes para tus trabajos</p>
    </div>
</div>
<?php endif; ?>

<style>
.costo-badge {
    background: #2196F3;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: bold;
    display: inline-block;
    margin-top: 5px;
}
.btn-solicitar {
    background: #FF9800;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85em;
    margin-top: 5px;
    display: inline-block;
}
.btn-solicitar:hover {
    background: #F57C00;
}
.solicitudes-lista {
    max-height: 200px;
    overflow-y: auto;
    margin-top: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
}
.solicitud-item {
    padding: 8px;
    border-bottom: 1px solid #eee;
    font-size: 0.9em;
}
.solicitud-item:last-child {
    border-bottom: none;
}
.alerta-envio {
    background: #E3F2FD;
    border: 2px solid #1565C0;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
}
.alerta-envio h3 {
    color: #1565C0;
    margin-bottom: 10px;
}
.btn-recibir {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}
.btn-recibir:hover {
    background: #45a049;
}
.filtros-container {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}
.filtros-container h3 {
    margin: 0 0 10px 0;
    font-size: 1em;
    color: #333;
}
.filtros-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: end;
}
.filtros-row .form-group {
    margin: 0;
    min-width: 120px;
}
.filtros-row label {
    font-size: 0.85em;
    margin-bottom: 3px;
    display: block;
}
.filtros-row select, .filtros-row input {
    padding: 6px 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 0.9em;
}
.btn-filtrar {
    background: #2196F3;
    color: white;
    border: none;
    padding: 7px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
}
.btn-filtrar:hover {
    background: #1976D2;
}
.btn-limpiar {
    background: #757575;
    color: white;
    border: none;
    padding: 7px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
    text-decoration: none;
    display: inline-block;
}
.btn-limpiar:hover {
    background: #616161;
}
.badge-pendiente {
    background: #FF9800;
    color: white;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 0.75em;
    font-weight: bold;
}
.badge-pausa {
    background: #9C27B0;
    color: white;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 0.75em;
    font-weight: bold;
}
.badge-naranja {
    background: #FF9800;
    color: white;
}
.badge-morado {
    background: #9C27B0;
    color: white;
}
</style>

<!-- Filtros -->
<div class="filtros-container">
    <h3>🔍 Filtrar Trabajos</h3>
    <form method="GET" action="<?= APP_URL ?>/public/tecnico/mis-trabajos">
        <div class="filtros-row">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos los estados</option>
                    <option value="todos" <?= ($filtros['estado'] ?? '') === 'todos' ? 'selected' : '' ?>>Todos (incluye entregados)</option>
                    <option value="activos" <?= ($filtros['estado'] ?? '') === 'activos' ? 'selected' : '' ?>>Activos (no entregados)</option>
                    <option value="pendiente_asignacion" <?= ($filtros['estado'] ?? '') === 'pendiente_asignacion' ? 'selected' : '' ?>>Pendiente Asignación</option>
                    <option value="asignado_sucursal" <?= ($filtros['estado'] ?? '') === 'asignado_sucursal' ? 'selected' : '' ?>>Asignado a Sucursal</option>
                    <option value="recibido" <?= ($filtros['estado'] ?? '') === 'recibido' ? 'selected' : '' ?>>Recibido</option>
                    <option value="en_reparacion" <?= ($filtros['estado'] ?? '') === 'en_reparacion' ? 'selected' : '' ?>>En Reparación</option>
                    <option value="completado" <?= ($filtros['estado'] ?? '') === 'completado' ? 'selected' : '' ?>>Completado</option>
                    <option value="entregado" <?= ($filtros['estado'] ?? '') === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Día</label>
                <select name="dia">
                    <option value="">Todos</option>
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <option value="<?= $i ?>" <?= ($filtros['dia'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Mes</label>
                <select name="mes">
                    <option value="">Todos</option>
                    <?php 
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    for ($i = 1; $i <= 12; $i++): 
                    ?>
                        <option value="<?= $i ?>" <?= ($filtros['mes'] ?? '') == $i ? 'selected' : '' ?>><?= $meses[$i-1] ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Año</label>
                <select name="anio">
                    <option value="">Todos</option>
                    <?php 
                    $anio_actual = date('Y');
                    for ($i = $anio_actual; $i >= $anio_actual - 5; $i--): 
                    ?>
                        <option value="<?= $i ?>" <?= ($filtros['anio'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn-filtrar">Filtrar</button>
                <a href="<?= APP_URL ?>/public/tecnico/mis-trabajos" class="btn-limpiar">Limpiar</a>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($solicitudes_enviadas)): ?>
<div class="alerta-envio">
    <h3>📦 Componentes Enviados por Almacén - Pendientes de Confirmación</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha Solicitud</th>
                    <th>Cliente</th>
                    <th>Equipo</th>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes_enviadas as $sol): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?></td>
                    <td><?= htmlspecialchars($sol['cliente_nombre'] . ' ' . $sol['cliente_ap']) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($sol['tipo_equipo']) ?></strong><br>
                        <small><?= htmlspecialchars($sol['equipo_marca'] . ' ' . $sol['equipo_modelo']) ?></small>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($sol['repuesto_nombre']) ?></strong><br>
                        <small><?= htmlspecialchars($sol['repuesto_codigo']) ?></small>
                    </td>
                    <td><strong><?= $sol['cantidad'] ?></strong></td>
                    <td>
                        <form method="POST" action="<?= APP_URL ?>/public/pedidos/confirmar-recibido-solicitud" style="display: inline;" onsubmit="return confirm('¿Confirmas que recibiste este componente?');">
                            <input type="hidden" name="solicitud_id" value="<?= $sol['id'] ?>">
                            <button type="submit" class="btn-recibir">✓ Confirmar Recibido</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($solicitudes_agotadas)): ?>
<div style="background: #FFEBEE; border: 2px solid #C62828; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px;">
    <h3 style="color: #C62828; margin-bottom: 10px;">⚠️ Componentes AGOTADOS - Sin Stock en Almacén</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha Solicitud</th>
                    <th>Cliente</th>
                    <th>Equipo</th>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes_agotadas as $sol): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?></td>
                    <td><?= htmlspecialchars($sol['cliente_nombre'] . ' ' . $sol['cliente_ap']) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($sol['tipo_equipo']) ?></strong><br>
                        <small><?= htmlspecialchars($sol['equipo_marca'] . ' ' . $sol['equipo_modelo']) ?></small>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($sol['repuesto_nombre']) ?></strong><br>
                        <small><?= htmlspecialchars($sol['repuesto_codigo']) ?></small>
                    </td>
                    <td><strong><?= $sol['cantidad'] ?></strong></td>
                    <td>
                        <?php if (!empty($sol['compra_externa_id'])): ?>
                            <?php if ($sol['ce_estado'] === 'pendiente'): ?>
                                <span style="background: #FF6F00; color: white; padding: 6px 12px; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">
                                    🛒 COMPRA EXTERNA EN PROCESO
                                </span>
                                <div style="font-size: 0.75rem; color: #FF6F00; margin-top: 4px;">
                                    Proveedor: <?= htmlspecialchars($sol['proveedor'] ?: 'Por definir') ?>
                                    <?php if (!empty($sol['ce_precio'])): ?>
                                        <br>Precio est: S/ <?= number_format($sol['ce_precio'], 2) ?>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($sol['ce_estado'] === 'recibida'): ?>
                                <span style="background: #2E7D32; color: white; padding: 6px 12px; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">
                                    ✓ COMPRA RECIBIDA - EN CAMINO
                                </span>
                            <?php else: ?>
                                <span class="badge badge-rojo" style="background: #C62828; color: white; padding: 6px 12px; border-radius: 4px; font-weight: bold;">
                                    ⚠️ YA NO HAY DISPONIBLE EN ALMACEN
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-rojo" style="background: #C62828; color: white; padding: 6px 12px; border-radius: 4px; font-weight: bold;">
                                ⚠️ YA NO HAY DISPONIBLE EN ALMACEN
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Resumen de Trabajos -->
<?php
$contadores = [
    'total' => count($trabajos),
    'pendiente' => 0,
    'recibido' => 0,
    'en_reparacion' => 0,
    'pausado' => 0,
    'completado' => 0,
    'entregado' => 0
];
foreach ($trabajos as $t) {
    $estado = $t['estado'];
    if (isset($contadores[$estado])) {
        $contadores[$estado]++;
    }
}
?>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-bottom: 20px;">
    <div style="background: #f5f5f5; padding: 10px; border-radius: 8px; text-align: center; border-left: 4px solid #2196F3;">
        <div style="font-size: 1.5em; font-weight: bold; color: #2196F3;"><?= $contadores['total'] ?></div>
        <div style="font-size: 0.8em; color: #666;">Total</div>
    </div>
    <div style="background: #FFF3E0; padding: 10px; border-radius: 8px; text-align: center; border-left: 4px solid #FF9800;">
        <div style="font-size: 1.5em; font-weight: bold; color: #FF9800;"><?= $contadores['pendiente'] ?></div>
        <div style="font-size: 0.8em; color: #666;">Pendientes</div>
    </div>
    <div style="background: #E3F2FD; padding: 10px; border-radius: 8px; text-align: center; border-left: 4px solid #2196F3;">
        <div style="font-size: 1.5em; font-weight: bold; color: #2196F3;"><?= $contadores['recibido'] ?></div>
        <div style="font-size: 0.8em; color: #666;">Recibidos</div>
    </div>
    <div style="background: #FFF8E1; padding: 10px; border-radius: 8px; text-align: center; border-left: 4px solid #FFC107;">
        <div style="font-size: 1.5em; font-weight: bold; color: #FFC107;"><?= $contadores['en_reparacion'] ?></div>
        <div style="font-size: 0.8em; color: #666;">En Reparación</div>
    </div>
    <div style="background: #F3E5F5; padding: 10px; border-radius: 8px; text-align: center; border-left: 4px solid #9C27B0;">
        <div style="font-size: 1.5em; font-weight: bold; color: #9C27B0;"><?= $contadores['pausado'] ?></div>
        <div style="font-size: 0.8em; color: #666;">En Pausa</div>
    </div>
    <div style="background: #E8F5E9; padding: 10px; border-radius: 8px; text-align: center; border-left: 4px solid #4CAF50;">
        <div style="font-size: 1.5em; font-weight: bold; color: #4CAF50;"><?= $contadores['completado'] ?></div>
        <div style="font-size: 0.8em; color: #666;">Completados</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Mis Trabajos Asignados</h2>
        <?php
        $hay_filtros = !empty($filtros['estado']) || !empty($filtros['dia']) || !empty($filtros['mes']) || !empty($filtros['anio']);
        if ($hay_filtros):
        ?>
        <span style="background: #FF9800; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8em;">
            🔍 Filtros activos - <?= count($trabajos) ?> trabajo(s)
        </span>
        <?php endif; ?>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha Asignación</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Equipo</th>
                    <th>Falla Reportada</th>
                    <th>Estado</th>
                    <th>Costo Reparación</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trabajos)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">No tienes trabajos asignados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($trabajos as $trabajo): ?>
                    <tr style="<?= $trabajo['estado'] === 'pausado' ? 'background: #F3E5F5;' : '' ?>">
                        <td><?= date('d/m/Y', strtotime($trabajo['fecha_asignacion'])) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($trabajo['cliente_nombre'] . ' ' . $trabajo['cliente_ap']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($trabajo['cliente_tel'] ?? '-') ?></td>
                        <td>
                            <strong><?= htmlspecialchars($trabajo['tipo_equipo']) ?></strong><br>
                            <small><?= htmlspecialchars($trabajo['marca'] . ' ' . $trabajo['modelo']) ?></small>
                        </td>
                        <td>
                            <small><?= htmlspecialchars(substr($trabajo['descripcion_falla'] ?? '', 0, 100)) ?></small>
                        </td>
                        <td>
                            <?php
                            $estado_class = 'badge-gris';
                            $estado_texto = $trabajo['estado'];
                            $estado_icono = '';
                            
                            if ($trabajo['estado'] === 'en_reparacion') {
                                $estado_class = 'badge-amarillo';
                                $estado_texto = 'En Reparación';
                                $estado_icono = '🔧';
                            } elseif ($trabajo['estado'] === 'completado') {
                                $estado_class = 'badge-verde';
                                $estado_texto = 'Completado';
                                $estado_icono = '✅';
                            } elseif ($trabajo['estado'] === 'asignado_sucursal') {
                                $estado_class = 'badge-azul';
                                $estado_texto = 'Asignado';
                                $estado_icono = '📍';
                            } elseif ($trabajo['estado'] === 'recibido') {
                                $estado_class = 'badge-azul';
                                $estado_texto = 'Recibido';
                                $estado_icono = '📥';
                            } elseif ($trabajo['estado'] === 'pendiente_asignacion') {
                                $estado_class = 'badge-naranja';
                                $estado_texto = 'Pendiente';
                                $estado_icono = '⏳';
                            } elseif ($trabajo['estado'] === 'pausado') {
                                $estado_class = 'badge-morado';
                                $estado_texto = 'En Pausa';
                                $estado_icono = '⏸️';
                            } elseif ($trabajo['estado'] === 'entregado') {
                                $estado_class = 'badge-verde';
                                $estado_texto = 'Entregado';
                                $estado_icono = '📦';
                            }
                            ?>
                            <span class="badge <?= $estado_class ?>"><?= $estado_icono ?> <?= $estado_texto ?></span>
                        </td>
                        <td>
                            <div class="costo-badge" id="costo-<?= $trabajo['id'] ?>">
                                S/ <?= number_format($trabajo['costo_reparacion'] ?? 0, 2) ?>
                            </div>
                            <?php if ($trabajo['estado'] !== 'completado' && $trabajo['estado'] !== 'asignado_sucursal'): ?>
                                <button class="btn-solicitar" onclick="abrirModalSolicitud(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>')">
                                    + Solicitar Componente
                                </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($trabajo['observaciones'])): ?>
                                <small style="color: #666;"><?= htmlspecialchars(nl2br(substr($trabajo['observaciones'], 0, 100))) ?><?= strlen($trabajo['observaciones']) > 100 ? '...' : '' ?></small>
                            <?php else: ?>
                                <span style="color: #ccc;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($trabajo['estado'] === 'asignado_sucursal'): ?>
                                <button onclick="confirmarRecibido(<?= $trabajo['id'] ?>)" class="btn btn-success btn-sm">✓ Recibido</button>
                                <button onclick="abrirModalRechazo(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>')" class="btn btn-danger btn-sm">✗ Rechazar</button>
                            <?php elseif ($trabajo['estado'] === 'recibido'): ?>
                                <button onclick="abrirModalActualizar(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>', 'recibido')" class="btn btn-primary btn-sm">▶ Iniciar Reparación</button>
                            <?php elseif ($trabajo['estado'] === 'en_reparacion'): ?>
                                <button onclick="abrirModalActualizar(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>', 'en_reparacion')" class="btn btn-primary btn-sm">⚙ Actualizar</button>
                            <?php elseif ($trabajo['estado'] === 'pausado'): ?>
                                <button onclick="abrirModalActualizar(<?= $trabajo['id'] ?>, '<?= htmlspecialchars($trabajo['tipo_equipo'] . ' ' . $trabajo['marca'] . ' ' . $trabajo['modelo']) ?>', 'pausado')" class="btn btn-warning btn-sm" style="background: #9C27B0;">▶ Reanudar</button>
                            <?php elseif ($trabajo['estado'] === 'completado'): ?>
                                <span style="color: #999; font-weight: bold;">✓ Finalizado</span>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalActualizar" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Actualizar Trabajo</h2>
            <button class="modal-close" onclick="cerrarModal()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/tecnico/actualizar-trabajo" id="formActualizar">
            <input type="hidden" name="equipo_id" id="equipo_id">
            <input type="hidden" name="estado_actual" id="estado_actual">
            <div class="form-group">
                <label>Equipo</label>
                <input type="text" id="equipo_nombre" readonly>
            </div>
            <div class="form-group">
                <label>Acción *</label>
                <select name="accion" id="select_accion" required onchange="validarAccion()">
                    <option value="">Seleccionar...</option>
                </select>
            </div>
            <div class="form-group" id="grupo_descripcion">
                <label id="label_descripcion">Descripción / Observaciones</label>
                <textarea name="descripcion" id="textarea_descripcion" rows="4" placeholder="Describe el trabajo realizado, repuestos utilizados, etc."></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalRechazo" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Rechazar Trabajo</h2>
            <button class="modal-close" onclick="cerrarModal()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/tecnico/rechazar-trabajo">
            <input type="hidden" name="equipo_id" id="rechazo_equipo_id">
            <div class="form-group">
                <label>Equipo</label>
                <input type="text" id="rechazo_equipo_nombre" readonly>
            </div>
            <div class="form-group">
                <label>Motivo del Rechazo *</label>
                <textarea name="motivo" rows="4" placeholder="Indica el motivo por el cual rechazas este trabajo..." required></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-danger">Rechazar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalSolicitud" class="modal-overlay">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Solicitar Componente</h2>
            <button class="modal-close" onclick="cerrarModalSolicitud()">×</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/tecnico/solicitar-componente" onsubmit="return validarSolicitud()">
            <input type="hidden" name="equipo_id" id="solicitud_equipo_id">
            <div class="form-group">
                <label>Equipo</label>
                <input type="text" id="solicitud_equipo_nombre" readonly>
            </div>
            <div class="form-group">
                <label>Repuesto *</label>
                <select name="repuesto_id" id="select_repuesto" required>
                    <option value="">Seleccionar repuesto...</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" id="cantidad_solicitud" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label>Precio Unitario</label>
                    <input type="text" id="precio_unitario_solicitud" readonly>
                </div>
            </div>
            <div id="alerta_stock" style="display: none; background: #FFEBEE; border: 2px solid #C62828; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                <strong style="color: #C62828;">⚠️ YA NO HAY DISPONIBLE EN ALMACEN</strong>
                <p style="color: #C62828; font-size: 0.9rem; margin: 5px 0 0 0;">Este componente no tiene stock disponible.</p>
            </div>
            <div id="info_disponible" style="display: none; background: #E8F5E9; border: 2px solid #2E7D32; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                <strong style="color: #2E7D32;">📦 Disponibles en Almacén: <span id="cantidad_disponible">0</span></strong>
                <p style="color: #2E7D32; font-size: 0.9rem; margin: 5px 0 0 0;">No puedes solicitar más de esta cantidad.</p>
            </div>
            <div class="form-group">
                <label>Total</label>
                <input type="text" id="total_solicitud" readonly style="font-weight: bold; color: #2196F3;">
            </div>
            <div class="form-group">
                <label>Motivo / Observaciones</label>
                <textarea name="motivo" rows="3" placeholder="Describe por qué necesitas este componente..."></textarea>
            </div>
            
            <div id="solicitudes_anteriores" style="display: none;">
                <label style="font-weight: bold; margin-bottom: 10px; display: block;">Solicitudes Anteriores:</label>
                <div class="solicitudes-lista" id="lista_solicitudes"></div>
            </div>
            
            <div style="background: #E3F2FD; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <strong>Costo Total de Reparación:</strong>
                <span id="costo_total_reparacion" style="font-size: 1.3em; color: #1976D2; margin-left: 10px;">S/ 0.00</span>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Solicitar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalSolicitud()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalActualizar(equipoId, equipoNombre, estadoActual) {
    document.getElementById('equipo_id').value = equipoId;
    document.getElementById('equipo_nombre').value = equipoNombre;
    document.getElementById('estado_actual').value = estadoActual;
    
    // Limpiar selección anterior
    document.getElementById('select_accion').value = '';
    document.getElementById('textarea_descripcion').value = '';
    
    // Configurar opciones según el estado actual
    const selectAccion = document.getElementById('select_accion');
    selectAccion.innerHTML = '<option value="">Seleccionar...</option>';
    
    if (estadoActual === 'recibido') {
        // Solo puede iniciar reparación
        selectAccion.innerHTML += '<option value="inicio_reparacion">▶ Iniciar Reparación</option>';
        document.getElementById('label_descripcion').textContent = 'Observaciones (opcional)';
    } else if (estadoActual === 'en_reparacion') {
        // Puede agregar nota técnica, pausar o completar
        selectAccion.innerHTML += '<option value="nota_tecnica">📝 Agregar Nota Técnica</option>';
        selectAccion.innerHTML += '<option value="pausado">⏸️ Pausar Trabajo</option>';
        selectAccion.innerHTML += '<option value="completado">✓ Marcar como Completado</option>';
        document.getElementById('label_descripcion').textContent = 'Descripción / Observaciones';
    } else if (estadoActual === 'pausado') {
        // Solo puede reanudar
        selectAccion.innerHTML += '<option value="reanudar">▶ Reanudar Trabajo</option>';
        document.getElementById('label_descripcion').textContent = 'Motivo de reanudación (opcional)';
    }
    
    document.getElementById('modalActualizar').classList.add('active');
}

function validarAccion() {
    const accion = document.getElementById('select_accion').value;
    const textarea = document.getElementById('textarea_descripcion');
    const label = document.getElementById('label_descripcion');
    
    // Si se selecciona pausar, hacer obligatorio el motivo
    if (accion === 'pausado') {
        textarea.setAttribute('required', 'required');
        textarea.placeholder = 'Indica la razón por la que se pausa el trabajo *';
        label.innerHTML = 'Motivo de Pausa <span style="color: red;">*</span>';
    } else {
        textarea.removeAttribute('required');
        textarea.placeholder = 'Describe el trabajo realizado, repuestos utilizados, etc.';
        label.textContent = 'Descripción / Observaciones';
    }
}

function cerrarModal() {
    document.getElementById('modalActualizar').classList.remove('active');
    document.getElementById('modalRechazo').classList.remove('active');
    document.getElementById('modalSolicitud').classList.remove('active');
}

function confirmarRecibido(equipoId) {
    if (confirm('¿Confirmas que has recibido este trabajo?')) {
        fetch('<?= APP_URL ?>/public/tecnico/confirmar-recibido', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'equipo_id=' + equipoId
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error al confirmar');
            }
        });
    }
}

function abrirModalRechazo(equipoId, equipoNombre) {
    document.getElementById('rechazo_equipo_id').value = equipoId;
    document.getElementById('rechazo_equipo_nombre').value = equipoNombre;
    document.getElementById('modalRechazo').classList.add('active');
}

function abrirModalSolicitud(equipoId, equipoNombre) {
    document.getElementById('solicitud_equipo_id').value = equipoId;
    document.getElementById('solicitud_equipo_nombre').value = equipoNombre;
    document.getElementById('modalSolicitud').classList.add('active');
    
    cargarRepuestos();
    cargarCostoEquipo(equipoId);
}

function cerrarModalSolicitud() {
    document.getElementById('modalSolicitud').classList.remove('active');
    document.getElementById('select_repuesto').value = '';
    document.getElementById('cantidad_solicitud').value = '1';
    document.getElementById('cantidad_solicitud').disabled = false;
    document.getElementById('precio_unitario_solicitud').value = '';
    document.getElementById('total_solicitud').value = '';
    document.getElementById('alerta_stock').style.display = 'none';
    document.getElementById('info_disponible').style.display = 'none';
}

function validarSolicitud() {
    const select = document.getElementById('select_repuesto');
    const option = select.options[select.selectedIndex];
    const disponibles = parseInt(option.dataset.disponibles || 0);
    const cantidad = parseInt(document.getElementById('cantidad_solicitud').value || 0);
    
    if (!select.value) {
        alert('Por favor selecciona un repuesto');
        return false;
    }
    
    if (disponibles <= 0) {
        alert('⚠️ YA NO HAY DISPONIBLE EN ALMACEN para este componente');
        return false;
    }
    
    if (cantidad <= 0) {
        alert('La cantidad debe ser mayor a 0');
        return false;
    }
    
    if (cantidad > disponibles) {
        alert('⚠️ No puedes solicitar más de ' + disponibles + ' unidades. Stock disponible: ' + disponibles);
        return false;
    }
    
    return true;
}

function cargarRepuestos() {
    fetch('<?= APP_URL ?>/public/tecnico/obtener-repuestos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('select_repuesto');
            select.innerHTML = '<option value="">Seleccionar repuesto...</option>';
            data.forEach(repuesto => {
                const disponibles = parseInt(repuesto.unidades_disponibles || 0);
                const option = document.createElement('option');
                option.value = repuesto.id;
                option.dataset.precio = repuesto.precio_unitario || 0;
                option.dataset.disponibles = disponibles;
                
                if (disponibles <= 0) {
                    option.textContent = `${repuesto.nombre} - ${repuesto.marca || 'Sin marca'} (AGOTADO) - S/ ${parseFloat(repuesto.precio_unitario || 0).toFixed(2)}`;
                    option.disabled = true;
                    option.style.color = '#C62828';
                } else {
                    option.textContent = `${repuesto.nombre} - ${repuesto.marca || 'Sin marca'} (Disp: ${disponibles}) - S/ ${parseFloat(repuesto.precio_unitario || 0).toFixed(2)}`;
                }
                select.appendChild(option);
            });
        });
}

function cargarCostoEquipo(equipoId) {
    fetch(`<?= APP_URL ?>/public/tecnico/obtener-costo-equipo?equipo_id=${equipoId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('costo_total_reparacion').textContent = `S/ ${parseFloat(data.total).toFixed(2)}`;
            
            if (data.solicitudes && data.solicitudes.length > 0) {
                const lista = document.getElementById('lista_solicitudes');
                lista.innerHTML = '';
                data.solicitudes.forEach(sol => {
                    const item = document.createElement('div');
                    item.className = 'solicitud-item';
                    
                    if (sol.estado === 'recibido') {
                        item.style.background = '#E8F5E9';
                        item.style.borderLeft = '3px solid #2E7D32';
                        item.innerHTML = `
                            <strong style="color: #2E7D32;">✓ ${sol.repuesto_nombre}</strong> - ${sol.cantidad} x S/ ${parseFloat(sol.precio_unitario).toFixed(2)} = S/ ${parseFloat(sol.total).toFixed(2)}
                            <br><small style="color: #2E7D32; font-weight: bold;">RECIBIDO - Suma al costo</small>
                            <br><small>${sol.motivo || 'Sin observaciones'}</small>
                        `;
                    } else if (sol.estado === 'agotado') {
                        item.style.background = '#FFEBEE';
                        item.style.borderLeft = '3px solid #C62828';
                        item.innerHTML = `
                            <strong style="color: #C62828;">⚠️ ${sol.repuesto_nombre}</strong> - ${sol.cantidad} unidades
                            <br><small style="color: #C62828; font-weight: bold;">AGOTADO - No suma al costo</small>
                            <br><small>${sol.motivo || 'Sin observaciones'}</small>
                        `;
                    } else if (sol.estado === 'enviado') {
                        item.style.background = '#E3F2FD';
                        item.style.borderLeft = '3px solid #1565C0';
                        item.innerHTML = `
                            <strong style="color: #1565C0;">📦 ${sol.repuesto_nombre}</strong> - ${sol.cantidad} unidades
                            <br><small style="color: #1565C0; font-weight: bold;">ENVIADO - Confirma recibido para sumar al costo</small>
                            <br><small>${sol.motivo || 'Sin observaciones'}</small>
                        `;
                    } else {
                        item.style.background = '#FFF3E0';
                        item.style.borderLeft = '3px solid #E65100';
                        item.innerHTML = `
                            <strong style="color: #E65100;">⏳ ${sol.repuesto_nombre}</strong> - ${sol.cantidad} unidades
                            <br><small style="color: #E65100; font-weight: bold;">SOLICITADO - Pendiente de envío</small>
                            <br><small>${sol.motivo || 'Sin observaciones'}</small>
                        `;
                    }
                    lista.appendChild(item);
                });
                document.getElementById('solicitudes_anteriores').style.display = 'block';
            } else {
                document.getElementById('solicitudes_anteriores').style.display = 'none';
            }
        });
}

document.getElementById('select_repuesto').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const precio = parseFloat(option.dataset.precio || 0);
    const disponibles = parseInt(option.dataset.disponibles || 0);
    const alertaStock = document.getElementById('alerta_stock');
    const infoDisponible = document.getElementById('info_disponible');
    const inputCantidad = document.getElementById('cantidad_solicitud');
    
    document.getElementById('precio_unitario_solicitud').value = `S/ ${precio.toFixed(2)}`;
    
    if (!option.value) {
        alertaStock.style.display = 'none';
        infoDisponible.style.display = 'none';
        inputCantidad.max = '';
        inputCantidad.value = 1;
        return;
    }
    
    if (disponibles <= 0) {
        alertaStock.style.display = 'block';
        infoDisponible.style.display = 'none';
        inputCantidad.max = 0;
        inputCantidad.value = 0;
        inputCantidad.disabled = true;
    } else {
        alertaStock.style.display = 'none';
        infoDisponible.style.display = 'block';
        document.getElementById('cantidad_disponible').textContent = disponibles;
        inputCantidad.max = disponibles;
        inputCantidad.disabled = false;
        if (parseInt(inputCantidad.value) > disponibles) {
            inputCantidad.value = disponibles;
        }
    }
    
    calcularTotalSolicitud();
});

document.getElementById('cantidad_solicitud').addEventListener('input', function() {
    const select = document.getElementById('select_repuesto');
    const option = select.options[select.selectedIndex];
    const disponibles = parseInt(option.dataset.disponibles || 0);
    const cantidad = parseInt(this.value || 0);
    
    if (disponibles > 0 && cantidad > disponibles) {
        this.value = disponibles;
        alert('⚠️ No puedes solicitar más de ' + disponibles + ' unidades');
    }
    
    calcularTotalSolicitud();
});

function calcularTotalSolicitud() {
    const precio = parseFloat(document.getElementById('precio_unitario_solicitud').value.replace('S/ ', '') || 0);
    const cantidad = parseInt(document.getElementById('cantidad_solicitud').value || 1);
    const total = precio * cantidad;
    document.getElementById('total_solicitud').value = `S/ ${total.toFixed(2)}`;
}

document.getElementById('modalActualizar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

document.getElementById('modalRechazo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

document.getElementById('modalSolicitud').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalSolicitud();
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
