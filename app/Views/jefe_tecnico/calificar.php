<?php $titulo = 'Calificación de Técnicos'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Filtrar por Mes</h2>
    </div>
    <form method="GET" action="<?= APP_URL ?>/public/jefe-tecnico/calificar" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Mes</label>
            <select name="mes" class="form-control">
                <?php
                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                for ($i = 1; $i <= 12; $i++):
                ?>
                    <option value="<?= $i ?>" <?= $mes == $i ? 'selected' : '' ?>><?= $meses[$i-1] ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Año</label>
            <input type="number" name="anio" value="<?= $anio ?>" class="form-control" min="2020" max="2030">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>

<?php if (!empty($tecnicos)): ?>
<div class="card">
    <div class="card-header">
        <h2>Calificar Técnico</h2>
    </div>
    <form method="POST" action="<?= APP_URL ?>/public/jefe-tecnico/guardar-calificacion" id="formCalificacion">
        <input type="hidden" name="mes" value="<?= $mes ?>">
        <input type="hidden" name="anio" value="<?= $anio ?>">
        
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Seleccionar Técnico *</label>
                <select name="tecnico_id" id="tecnico_id" class="form-control" required onchange="cargarAsistencias()">
                    <option value="">-- Seleccione un técnico --</option>
                    <?php foreach ($tecnicos as $t): ?>
                        <option value="<?= $t['id'] ?>">
                            <?= htmlspecialchars($t['nombre_completo']) ?> (<?= htmlspecialchars($t['carnet']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Puntuación Trabajo (1-10) *</label>
                <input type="number" name="puntuacion_trabajo" id="puntuacion_trabajo" min="1" max="10" step="0.5" class="form-control" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Puntuación Asistencia (1-10) *</label>
                <input type="number" name="puntuacion_asistencia" id="puntuacion_asistencia" min="1" max="10" step="0.5" class="form-control" required>
            </div>
        </div>
        
        <div id="infoAsistencias" style="display: none; background: #f5f5f5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="margin-top: 0; color: #333;">Resumen de Asistencias del Mes</h4>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div>
                    <strong>Total Días:</strong> <span id="totalDias">0</span>
                </div>
                <div>
                    <strong>Presentes:</strong> <span id="totalPresentes" style="color: #4CAF50;">0</span>
                </div>
                <div>
                    <strong>Tardanzas:</strong> <span id="totalTardanzas" style="color: #FF9800;">0</span>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3" placeholder="Comentarios adicionales..."></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar Calificación</button>
    </form>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h2>Sin Técnicos</h2>
    </div>
    <div style="text-align: center; padding: 40px; color: #666;">
        <p style="font-size: 48px; margin-bottom: 20px;">👷</p>
        <p>No hay técnicos registrados en su sucursal</p>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($ranking)): ?>
<div class="card">
    <div class="card-header">
        <h2>🏆 Ranking Mensual - <?= $meses[$mes-1] ?> <?= $anio ?></h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Técnico</th>
                    <th>Carnet</th>
                    <th>Puntuación Trabajo</th>
                    <th>Puntuación Asistencia</th>
                    <th>Puntuación Total</th>
                    <th>Mensaje</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ranking as $index => $r): ?>
                    <tr>
                        <td>
                            <?php if ($index == 0): ?>
                                <span style="font-size: 24px;">🥇</span>
                            <?php elseif ($index == 1): ?>
                                <span style="font-size: 24px;">🥈</span>
                            <?php elseif ($index == 2): ?>
                                <span style="font-size: 24px;">🥉</span>
                            <?php else: ?>
                                <strong><?= $index + 1 ?></strong>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($r['tecnico_nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($r['carnet']) ?></td>
                        <td>
                            <span class="badge badge-azul"><?= number_format($r['puntuacion_trabajo'], 2) ?></span>
                        </td>
                        <td>
                            <span class="badge badge-gris"><?= number_format($r['puntuacion_asistencia'], 2) ?></span>
                        </td>
                        <td>
                            <?php
                            $total = $r['puntuacion_total'];
                            $class = $total >= 7 ? 'badge-verde' : ($total >= 5 ? 'badge-amarillo' : 'badge-rojo');
                            ?>
                            <span class="badge <?= $class ?>" style="font-size: 16px;"><?= number_format($total, 2) ?></span>
                        </td>
                        <td>
                            <?php if ($r['puntuacion_total'] > 5): ?>
                                <span style="color: #4CAF50; font-weight: bold;">✓ Sigue así y mejora</span>
                            <?php else: ?>
                                <span style="color: #f44336; font-weight: bold;">⚠ Mejora más o si no tendrás sanciones</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['observaciones'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
<div id="toast-exito" style="position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 9999; display: flex; align-items: center; gap: 10px;">
    <span style="font-size: 1.5em;">✓</span>
    <div>
        <strong><?= $_SESSION['mensaje_exito'] ?></strong>
    </div>
</div>
<?php unset($_SESSION['mensaje_exito']); ?>
<script>
    setTimeout(() => {
        const toast = document.getElementById('toast-exito');
        if (toast) toast.remove();
    }, 3000);
</script>
<?php endif; ?>

<script>
function cargarAsistencias() {
    const tecnicoId = document.getElementById('tecnico_id').value;
    const mes = document.querySelector('select[name="mes"]').value;
    const anio = document.querySelector('input[name="anio"]').value;
    
    if (!tecnicoId) {
        document.getElementById('infoAsistencias').style.display = 'none';
        return;
    }
    
    fetch('<?= APP_URL ?>/public/jefe-tecnico/obtener-asistencias?tecnico_id=' + tecnicoId + '&mes=' + mes + '&anio=' + anio)
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalDias').textContent = data.total || 0;
            document.getElementById('totalPresentes').textContent = data.presentes || 0;
            document.getElementById('totalTardanzas').textContent = data.tardanzas || 0;
            document.getElementById('infoAsistencias').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
</script>


<?php $css_extra = 'jefe_tecnico.css'; $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
