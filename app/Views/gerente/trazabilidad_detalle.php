<?php $titulo = 'Trazabilidad - Equipo #' . $equipo['id']; $css_extra = 'gerente.css'; ob_start(); ?>


<a href="<?= APP_URL ?>/public/gerente/trazabilidad" class="btn-volver">
    <span>&#8592;</span> Volver a Trazabilidad
</a>

<div class="equipo-header">
    <div>
        <h2>Equipo #<?= $equipo['id'] ?> - <?= htmlspecialchars(ucfirst($equipo['tipo_equipo'])) ?></h2>
        <div class="equipo-info-grid">
            <div class="info-item">
                <span class="info-label">Marca / Modelo</span>
                <span class="info-value"><?= htmlspecialchars(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Numero de Serie</span>
                <span class="info-value"><?= htmlspecialchars($equipo['numero_serie'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Descripcion de Falla</span>
                <span class="info-value"><?= htmlspecialchars($equipo['descripcion_falla'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Accesorios</span>
                <span class="info-value"><?= htmlspecialchars($equipo['accesorios'] ?? 'Ninguno') ?></span>
            </div>
        </div>
    </div>
    <div>
        <h2>Informacion del Cliente y Estado</h2>
        <div class="equipo-info-grid">
            <div class="info-item">
                <span class="info-label">Cliente</span>
                <span class="info-value"><?= htmlspecialchars($equipo['cliente_nombre'] . ' ' . $equipo['cliente_ap']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">DNI / Telefono</span>
                <span class="info-value"><?= htmlspecialchars($equipo['cliente_dni'] ?? '') ?> / <?= htmlspecialchars($equipo['cliente_tel'] ?? '') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Sucursal Actual</span>
                <span class="info-value"><?= htmlspecialchars($equipo['sucursal_actual_nombre'] ?? 'Sin asignar') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Estado Actual</span>
                <span class="info-value">
                    <?php
                    $estado_labels = [
                        'registrado' => 'Registrado',
                        'pendiente_asignacion' => 'Pendiente Asignacion',
                        'asignado_sucursal' => 'Asignado a Sucursal',
                        'recibido' => 'Recibido',
                        'en_reparacion' => 'En Reparacion',
                        'completado' => 'Completado',
                        'entregado' => 'Entregado',
                    ];
                    ?>
                    <span class="estado-badge-grande estado-<?= $equipo['estado'] ?>">
                        <?= $estado_labels[$equipo['estado']] ?? $equipo['estado'] ?>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="timeline-container">
    <h2>Historial Completo del Equipo</h2>
    
    <?php if (empty($timeline)): ?>
        <div class="sin-timeline">
            <div class="icono">&#128203;</div>
            <p>No hay eventos registrados en el historial de este equipo</p>
        </div>
    <?php else: ?>
        <?php
        $evento_labels = [
            'registro' => 'Equipo Registrado',
            'asignacion_sucursal' => 'Asignacion a Sucursal',
            'recibido' => 'Tecnico Confirmo Recepcion',
            'inicio_reparacion' => 'Inicio de Reparacion',
            'nota_tecnica' => 'Nota Tecnica',
            'completado' => 'Reparacion Completada',
            'pausado' => 'Trabajo Pausado',
            'rechazado' => 'Trabajo Rechazado',
            'entrega' => 'Equipo Entregado al Cliente',
        ];
        ?>
        <div class="timeline">
            <?php foreach ($timeline as $evento): ?>
                <?php
                $evento_tipo = $evento['evento'];
                if (strpos($evento_tipo, 'asignacion_tecnico') === 0) {
                    $evento_tipo = 'asignacion_tecnico';
                }
                $label = $evento_labels[$evento_tipo] ?? ucfirst(str_replace('_', ' ', $evento['evento']));
                ?>
                <div class="timeline-item evento-<?= $evento_tipo ?>">
                    <div class="timeline-header">
                        <span class="timeline-evento"><?= $label ?></span>
                        <span class="timeline-fecha">
                            <span class="fecha-icon">&#128197;</span>
                            <?= date('d/m/Y', strtotime($evento['fecha'])) ?>
                            <span style="margin-left: 4px;">&#128336;</span>
                            <?= date('H:i:s', strtotime($evento['fecha'])) ?>
                        </span>
                    </div>
                    <div class="timeline-body">
                        <?php if (!empty($evento['persona_nombre'])): ?>
                        <div class="timeline-persona">
                            <span class="nombre"><?= htmlspecialchars(trim($evento['persona_nombre'])) ?></span>
                            <span class="rol"><?= htmlspecialchars($evento['persona_rol']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($evento['sucursal_nombre'])): ?>
                        <div class="timeline-sucursal">
                            &#127970; <?= htmlspecialchars($evento['sucursal_nombre']) ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($evento['descripcion'])): ?>
                        <div class="timeline-descripcion">
                            <?= htmlspecialchars($evento['descripcion']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
