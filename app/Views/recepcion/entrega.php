<?php $titulo = 'Entrega de Equipo'; ob_start(); ?>

<style>
.componentes-entrega {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
}
.componente-fila {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #ddd;
}
.componente-fila:last-child {
    border-bottom: none;
}
.resumen-costos {
    background: #E3F2FD;
    border: 2px solid #1565C0;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}
.resumen-linea {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 0.95rem;
}
.resumen-total {
    border-top: 2px solid #1565C0;
    margin-top: 8px;
    padding-top: 8px;
    font-size: 1.2rem;
    font-weight: bold;
    color: #1565C0;
}
.diferencia-positiva {
    color: #C62828;
    font-weight: bold;
}
.diferencia-negativa {
    color: #2E7D32;
    font-weight: bold;
}
</style>

<div class="card">
    <div class="card-header">
        <h2>Formulario de Entrega de Equipo</h2>
    </div>
    
    <form method="POST" action="<?= APP_URL ?>/public/recepcion/procesar-entrega" id="formEntrega">
        <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">
        
        <!-- Información del Cliente -->
        <div class="seccion" style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-left: 4px solid #2196f3;">
            <h3 style="margin: 0 0 15px 0; color: #2196f3;">👤 Información del Cliente</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <strong>Nombre:</strong> <?= htmlspecialchars($equipo['cliente_nombre'] . ' ' . $equipo['cliente_ap']) ?>
                </div>
                <div>
                    <strong>Teléfono:</strong> <?= htmlspecialchars($equipo['cliente_tel']) ?>
                </div>
            </div>
        </div>
        
        <!-- Información del Equipo -->
        <div class="seccion" style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-left: 4px solid #4caf50;">
            <h3 style="margin: 0 0 15px 0; color: #4caf50;">📱 Información del Equipo</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($equipo['tipo_equipo'])) ?>
                </div>
                <div>
                    <strong>Marca/Modelo:</strong> <?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?>
                </div>
                <div>
                    <strong>Fecha de Registro:</strong> <?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?>
                </div>
                <div>
                    <strong>Fecha Estimada de Entrega:</strong> 
                    <?php if (!empty($equipo['fecha_estimada_entrega'])): ?>
                        <span style="color: #1976d2; font-weight: bold;"><?= date('d/m/Y', strtotime($equipo['fecha_estimada_entrega'])) ?></span>
                    <?php else: ?>
                        <span style="color: #999;">No definida</span>
                    <?php endif; ?>
                </div>
                <div style="grid-column: 1 / -1;">
                    <strong>Falla Reportada:</strong><br>
                    <?= htmlspecialchars($equipo['descripcion_falla']) ?>
                </div>
            </div>
        </div>
        
        <!-- Componentes Usados y Costos -->
        <?php if (!empty($componentes)): ?>
        <div class="seccion" style="background: #e8f5e9; padding: 20px; margin-bottom: 20px; border-left: 4px solid #4caf50;">
            <h3 style="margin: 0 0 15px 0; color: #4caf50;">🔧 Componentes Usados en la Reparación</h3>
            
            <div class="componentes-entrega">
                <?php foreach ($componentes as $comp): ?>
                    <div class="componente-fila">
                        <div>
                            <strong><?= htmlspecialchars($comp['repuesto_nombre']) ?></strong>
                            <?php if (!empty($comp['repuesto_codigo'])): ?>
                                <br><small style="color: #666;">Código: <?= htmlspecialchars($comp['repuesto_codigo']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="text-align: right;">
                            <strong><?= $comp['cantidad'] ?> x S/ <?= number_format($comp['precio_unitario'], 2) ?></strong>
                            <br><span style="color: #1565C0; font-weight: bold;">= S/ <?= number_format($comp['total'], 2) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="resumen-costos">
                <div class="resumen-linea">
                    <span>Costo Estimado Original:</span>
                    <span>S/ <?= number_format($equipo['costo_estimado'] ?? 0, 2) ?></span>
                </div>
                <div class="resumen-linea">
                    <span>Costo de Componentes:</span>
                    <span>S/ <?= number_format($equipo['costo_reparacion'] ?? 0, 2) ?></span>
                </div>
                <?php 
                $costo_estimado = $equipo['costo_estimado'] ?? 0;
                $costo_reparacion = $equipo['costo_reparacion'] ?? 0;
                $diferencia = $costo_reparacion - $costo_estimado;
                if ($diferencia != 0): 
                ?>
                <div class="resumen-linea">
                    <span>Diferencia:</span>
                    <span class="<?= $diferencia > 0 ? 'diferencia-positiva' : 'diferencia-negativa' ?>">
                        <?= $diferencia > 0 ? '+' : '' ?>S/ <?= number_format($diferencia, 2) ?>
                    </span>
                </div>
                <?php endif; ?>
                <div class="resumen-linea resumen-total">
                    <span>Total Componentes:</span>
                    <span>S/ <?= number_format($costo_reparacion, 2) ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Ajuste de Precio -->
        <div class="seccion" style="background: #fff3e0; padding: 20px; margin-bottom: 20px; border-left: 4px solid #ff9800;">
            <h3 style="margin: 0 0 15px 0; color: #ff9800;">💰 Ajuste de Precio de Reparación</h3>
            
            <div class="form-group">
                <label for="costo_estimado">Costo Estimado Original:</label>
                <input type="text" id="costo_estimado" value="S/ <?= number_format($equipo['costo_estimado'] ?? 0, 2) ?>" readonly style="background: #f5f5f5;">
            </div>
            
            <?php if (!empty($componentes)): ?>
            <div class="form-group">
                <label for="costo_componentes">Costo de Componentes (calculado):</label>
                <input type="text" id="costo_componentes" value="S/ <?= number_format($equipo['costo_reparacion'] ?? 0, 2) ?>" readonly style="background: #E8F5E9; color: #2E7D32; font-weight: bold;">
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="costo_final">Costo Final de Reparación: *</label>
                <input type="number" id="costo_final" name="costo_final" step="0.01" min="0" required 
                       value="<?= number_format($equipo['costo_reparacion'] ?? 0, 2, '.', '') ?>" 
                       placeholder="Ingrese el costo final">
                <small style="color: #666;">Puede ajustar el precio según el trabajo realizado. Se sugiere el costo de componentes como base.</small>
            </div>
        </div>
        
        <!-- Firma de Conformidad -->
        <div class="seccion" style="background: #e8f5e9; padding: 20px; margin-bottom: 20px; border-left: 4px solid #4caf50;">
            <h3 style="margin: 0 0 15px 0; color: #4caf50;">✍️ Firma de Conformidad del Cliente</h3>
            <p style="margin-bottom: 15px;">El cliente firma conforme con la reparación realizada:</p>
            
            <div style="border: 2px dashed #4caf50; border-radius: 8px; padding: 10px; background: white;">
                <canvas id="canvasFirma" width="600" height="200" style="border: 1px solid #ccc; cursor: crosshair; width: 100%;"></canvas>
                <input type="hidden" name="firma_entrega" id="firmaEntrega">
            </div>
            
            <div style="margin-top: 10px;">
                <button type="button" onclick="limpiarFirma()" class="btn btn-outline">🗑️ Limpiar Firma</button>
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div style="text-align: center; margin-top: 30px;">
            <button type="submit" class="btn btn-success" style="padding: 15px 40px; font-size: 16px;">
                ✓ Confirmar Entrega y Generar Recibo
            </button>
            <a href="<?= APP_URL ?>/public/recepcion/equipos-listos" class="btn btn-outline" style="padding: 15px 40px; font-size: 16px;">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
const canvas = document.getElementById('canvasFirma');
const ctx = canvas.getContext('2d');
let drawing = false;
let lastX = 0;
let lastY = 0;

// Ajustar tamaño del canvas
function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = 200;
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
}

resizeCanvas();
window.addEventListener('resize', resizeCanvas);

// Eventos de mouse
canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseout', stopDrawing);

// Eventos táctiles
canvas.addEventListener('touchstart', handleTouchStart);
canvas.addEventListener('touchmove', handleTouchMove);
canvas.addEventListener('touchend', stopDrawing);

function startDrawing(e) {
    drawing = true;
    const rect = canvas.getBoundingClientRect();
    lastX = e.clientX - rect.left;
    lastY = e.clientY - rect.top;
}

function draw(e) {
    if (!drawing) return;
    
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(x, y);
    ctx.stroke();
    
    lastX = x;
    lastY = y;
}

function stopDrawing() {
    drawing = false;
}

function handleTouchStart(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const rect = canvas.getBoundingClientRect();
    startDrawing({
        clientX: touch.clientX,
        clientY: touch.clientY
    });
}

function handleTouchMove(e) {
    e.preventDefault();
    const touch = e.touches[0];
    draw({
        clientX: touch.clientX,
        clientY: touch.clientY
    });
}

function limpiarFirma() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Validar formulario antes de enviar
document.getElementById('formEntrega').addEventListener('submit', function(e) {
    const costoFinal = document.getElementById('costo_final').value;
    const firmaCanvas = document.getElementById('canvasFirma');
    
    // Verificar que haya firma
    const blank = document.createElement('canvas');
    blank.width = firmaCanvas.width;
    blank.height = firmaCanvas.height;
    
    if (firmaCanvas.toDataURL() === blank.toDataURL()) {
        e.preventDefault();
        alert('Por favor, el cliente debe firmar para confirmar la entrega');
        return false;
    }
    
    // Guardar firma como base64
    document.getElementById('firmaEntrega').value = firmaCanvas.toDataURL('image/png');
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
