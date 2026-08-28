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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asignaciones)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No hay trabajos asignados</td>
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
                                <?php elseif ($a['equipo_estado'] === 'recibido'): ?>
                                    <span class="badge badge-azul">Recibido</span>
                                <?php elseif ($a['equipo_estado'] === 'asignado_sucursal'): ?>
                                    <span class="badge badge-azul">Esperando aceptación de trabajo</span>
                                <?php else: ?>
                                    <span class="badge badge-gris"><?= ucfirst(str_replace('_', ' ', $a['equipo_estado'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($a['equipo_estado'] === 'completado'): ?>
                                    <button onclick="verDetallesCompletado(<?= $a['equipo_id'] ?>, '<?= htmlspecialchars($a['equipo_tipo'] . ' - ' . $a['equipo_marca'] . ' ' . $a['equipo_modelo']) ?>', '<?= htmlspecialchars($a['tecnico_nombre'] . ' ' . $a['tecnico_apellido']) ?>')" class="btn btn-primary btn-sm">Ver Detalles</button>
                                <?php elseif ($a['equipo_estado'] === 'asignado_sucursal'): ?>
                                    <span style="color: #999;">Pendiente</span>
                                <?php else: ?>
                                    <span style="color: #999;">En proceso</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalDetalles" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Detalles del Trabajo Completado</h2>
            <button class="modal-close" onclick="cerrarModalDetalles()">×</button>
        </div>
        <div id="contenidoDetalles">
            <div style="text-align: center; padding: 20px;">
                <p>Cargando detalles...</p>
            </div>
        </div>
    </div>
</div>

<script>
function verDetallesCompletado(equipoId, equipoNombre, tecnicoNombre) {
    document.getElementById('modalDetalles').classList.add('active');
    
    fetch('<?= APP_URL ?>/public/jefe-tecnico/obtener-detalles-equipo?equipo_id=' + equipoId)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div style="padding: 20px;">
                    <div style="margin-bottom: 20px;">
                        <h3 style="margin-bottom: 10px; color: #333;">Equipo</h3>
                        <p><strong>${equipoNombre}</strong></p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h3 style="margin-bottom: 10px; color: #333;">Técnico</h3>
                        <p>${tecnicoNombre}</p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <h3 style="margin-bottom: 10px; color: #333;">Historial de Seguimiento</h3>
                        <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; max-height: 300px; overflow-y: auto;">
            `;
            
            if (data.seguimiento && data.seguimiento.length > 0) {
                data.seguimiento.forEach(item => {
                    let fecha = new Date(item.fecha_registro).toLocaleString('es-ES');
                    let accion = item.accion.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    html += `
                        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <strong style="color: #D32F2F;">${accion}</strong>
                                <small style="color: #666;">${fecha}</small>
                            </div>
                            ${item.descripcion ? `<p style="margin: 0; color: #555;">${item.descripcion}</p>` : ''}
                        </div>
                    `;
                });
            } else {
                html += '<p style="color: #999;">No hay registros de seguimiento</p>';
            }
            
            html += `
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button onclick="aprobarTrabajo(${equipoId})" class="btn btn-success">Aprobar y Marcar como Entregado</button>
                        <button onclick="cerrarModalDetalles()" class="btn btn-outline">Cerrar</button>
                    </div>
                </div>
            `;
            
            document.getElementById('contenidoDetalles').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('contenidoDetalles').innerHTML = `
                <div style="padding: 20px; text-align: center; color: #D32F2F;">
                    <p>Error al cargar los detalles</p>
                </div>
            `;
        });
}

function cerrarModalDetalles() {
    document.getElementById('modalDetalles').classList.remove('active');
}

function aprobarTrabajo(equipoId) {
    if (confirm('¿Estás seguro de aprobar y marcar este trabajo como entregado?')) {
        fetch('<?= APP_URL ?>/public/jefe-tecnico/aprobar-trabajo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'equipo_id=' + equipoId
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error al aprobar el trabajo');
            }
        });
    }
}

document.getElementById('modalDetalles').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalDetalles();
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
