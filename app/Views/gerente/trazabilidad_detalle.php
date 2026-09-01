<?php $titulo = 'Trazabilidad - Equipo #' . $equipo['id']; ob_start(); ?>

<style>
.equipo-header {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--sombra);
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.equipo-header h2 {
    margin-bottom: 15px;
    color: var(--negro);
    font-size: 1.3rem;
}
.equipo-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.info-item {
    display: flex;
    flex-direction: column;
}
.info-label {
    font-size: 0.75rem;
    color: var(--gris);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--negro);
}

.estado-badge-grande {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
}
.estado-registrado { background: #E3F2FD; color: #1565C0; }
.estado-pendiente_asignacion { background: #FFF3E0; color: #E65100; }
.estado-asignado_sucursal { background: #F3E5F5; color: #6A1B9A; }
.estado-recibido { background: #E8F5E9; color: #2E7D32; }
.estado-en_reparacion { background: #FFF8E1; color: #F57F17; }
.estado-completado { background: #E0F7FA; color: #00695C; }
.estado-entregado { background: #E8F5E9; color: #1B5E20; }

.timeline-container {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--sombra);
}
.timeline-container h2 {
    margin-bottom: 20px;
    color: var(--negro);
    font-size: 1.3rem;
}

.timeline {
    position: relative;
    padding-left: 40px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, var(--rojo), var(--gris-claro));
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 24px;
    padding: 16px 20px;
    background: var(--blanco-humo);
    border-radius: 10px;
    border-left: 4px solid var(--rojo);
    transition: transform 0.2s;
}
.timeline-item:hover {
    transform: translateX(4px);
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -33px;
    top: 20px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--rojo);
    border: 3px solid white;
    box-shadow: 0 0 0 2px var(--rojo);
}

.timeline-item.evento-registro { border-left-color: #1565C0; }
.timeline-item.evento-registro::before { background: #1565C0; box-shadow: 0 0 0 2px #1565C0; }
.timeline-item.evento-asignacion_sucursal { border-left-color: #6A1B9A; }
.timeline-item.evento-asignacion_sucursal::before { background: #6A1B9A; box-shadow: 0 0 0 2px #6A1B9A; }
.timeline-item.evento-asignacion_tecnico,
.timeline-item[class*="asignacion_tecnico"] { border-left-color: #E65100; }
.timeline-item.evento-asignacion_tecnico::before,
.timeline-item[class*="asignacion_tecnico"]::before { background: #E65100; box-shadow: 0 0 0 2px #E65100; }
.timeline-item.evento-recibido { border-left-color: #2E7D32; }
.timeline-item.evento-recibido::before { background: #2E7D32; box-shadow: 0 0 0 2px #2E7D32; }
.timeline-item.evento-inicio_reparacion { border-left-color: #F57F17; }
.timeline-item.evento-inicio_reparacion::before { background: #F57F17; box-shadow: 0 0 0 2px #F57F17; }
.timeline-item.evento-nota_tecnica { border-left-color: #757575; }
.timeline-item.evento-nota_tecnica::before { background: #757575; box-shadow: 0 0 0 2px #757575; }
.timeline-item.evento-completado { border-left-color: #00695C; }
.timeline-item.evento-completado::before { background: #00695C; box-shadow: 0 0 0 2px #00695C; }
.timeline-item.evento-entrega { border-left-color: #1B5E20; }
.timeline-item.evento-entrega::before { background: #1B5E20; box-shadow: 0 0 0 2px #1B5E20; }
.timeline-item.evento-rechazado { border-left-color: #D32F2F; }
.timeline-item.evento-rechazado::before { background: #D32F2F; box-shadow: 0 0 0 2px #D32F2F; }

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 8px;
}
.timeline-evento {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--negro);
}
.timeline-fecha {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: var(--gris);
    font-weight: 600;
}
.timeline-fecha .fecha-icon {
    font-size: 1rem;
}
.timeline-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.timeline-persona {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
}
.timeline-persona .nombre {
    font-weight: 600;
    color: var(--negro);
}
.timeline-persona .rol {
    background: var(--gris-claro);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--gris-oscuro);
}
.timeline-sucursal {
    font-size: 0.8rem;
    color: var(--gris);
}
.timeline-descripcion {
    font-size: 0.85rem;
    color: var(--gris-oscuro);
    margin-top: 4px;
    padding: 8px 12px;
    background: white;
    border-radius: 6px;
    border-left: 3px solid var(--gris-claro);
}

.btn-volver {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--negro);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 16px;
    transition: background 0.3s;
}
.btn-volver:hover {
    background: var(--negro-suave);
    color: white;
}

.sin-timeline {
    text-align: center;
    padding: 40px;
    color: var(--gris);
}
.sin-timeline .icono {
    font-size: 3rem;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .equipo-header {
        grid-template-columns: 1fr;
    }
    .equipo-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

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
