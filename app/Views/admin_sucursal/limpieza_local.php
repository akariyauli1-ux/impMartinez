<?php $titulo = 'Limpieza del Local'; ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h2>Registrar Limpieza del Local</h2>
    </div>
    
    <form method="POST" action="<?= APP_URL ?>/public/admin-sucursal/guardar-limpieza-local">
        <div class="form-group">
            <label for="fecha">Fecha *</label>
            <input type="date" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="hora">Hora *</label>
            <input type="time" id="hora" name="hora" value="<?= date('H:i') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Áreas Limpiadas *</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-top: 10px;">
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Área de recepción" style="width: auto;">
                    <span>Área de recepción</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Taller" style="width: auto;">
                    <span>Taller</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Baño" style="width: auto;">
                    <span>Baño</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Almacén" style="width: auto;">
                    <span>Almacén</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Vitrinas" style="width: auto;">
                    <span>Vitrinas</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Área de espera" style="width: auto;">
                    <span>Área de espera</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Mostrador" style="width: auto;">
                    <span>Mostrador</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Oficina" style="width: auto;">
                    <span>Oficina</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Pisos" style="width: auto;">
                    <span>Pisos</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="areas_limpiadas[]" value="Ventanas" style="width: auto;">
                    <span>Ventanas</span>
                </label>
            </div>
            <div style="margin-top: 15px;">
                <label for="otra_area">Otra área (especificar):</label>
                <input type="text" id="otra_area" name="otra_area" placeholder="Especificar otra área..." style="margin-top: 5px;">
            </div>
        </div>
        
        <div class="form-group">
            <label>Productos Utilizados</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-top: 10px;">
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Desinfectante" style="width: auto;">
                    <span>Desinfectante</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Limpiavidrios" style="width: auto;">
                    <span>Limpiavidrios</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Alcohol isopropílico" style="width: auto;">
                    <span>Alcohol isopropílico</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Aire comprimido" style="width: auto;">
                    <span>Aire comprimido</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Escoba" style="width: auto;">
                    <span>Escoba</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Trapeador" style="width: auto;">
                    <span>Trapeador</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Trapo/microfibra" style="width: auto;">
                    <span>Trapo/microfibra</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Jabón" style="width: auto;">
                    <span>Jabón</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                    <input type="checkbox" name="productos_utilizados[]" value="Desengrasante" style="width: auto;">
                    <span>Desengrasante</span>
                </label>
            </div>
            <div style="margin-top: 15px;">
                <label for="otro_producto">Otro producto (especificar):</label>
                <input type="text" id="otro_producto" name="otro_producto" placeholder="Especificar otro producto..." style="margin-top: 5px;">
            </div>
        </div>
        
        <div class="form-group">
            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="3" placeholder="Observaciones adicionales sobre la limpieza..."></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar Limpieza</button>
    </form>
</div>

<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2>Historial de Limpieza del Local</h2>
    </div>
    
    <?php if (empty($historial)): ?>
        <p style="text-align: center; padding: 20px; color: #666;">No hay registros de limpieza aún.</p>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Áreas Limpiadas</th>
                        <th>Productos</th>
                        <th>Observaciones</th>
                        <th>Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                            <td><?= date('H:i', strtotime($h['hora'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($h['areas_limpiadas'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($h['productos_utilizados'] ?? '-')) ?></td>
                            <td><?= nl2br(htmlspecialchars($h['observaciones'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars($h['registrado_por_nombre'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('input[name="areas_limpiadas[]"]:checked');
    const otraArea = document.getElementById('otra_area').value.trim();
    
    if (checkboxes.length === 0 && otraArea === '') {
        e.preventDefault();
        alert('Por favor selecciona al menos un área o especifica otra área');
        return false;
    }
});
</script>
