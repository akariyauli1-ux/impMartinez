<?php $titulo = 'Nuevo Pedido de Almacen'; ob_start(); ?>

<style>
.form-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--sombra);
    max-width: 700px;
    margin: 0 auto;
}
.form-card h2 {
    margin-bottom: 20px;
    color: var(--negro);
}
.repuesto-info {
    background: var(--blanco-humo);
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 8px;
    font-size: 0.85rem;
    display: none;
}
.repuesto-info.visible {
    display: block;
}
.repuesto-info .stock-ok {
    color: #2E7D32;
    font-weight: 600;
}
.repuesto-info .stock-bajo {
    color: #C62828;
    font-weight: 600;
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
}
.btn-volver:hover {
    background: var(--negro-suave);
    color: white;
}
</style>

<a href="<?= APP_URL ?>/public/pedidos" class="btn-volver">
    <span>&#8592;</span> Volver a Mis Pedidos
</a>

<div class="form-card">
    <h2>Solicitar Repuesto</h2>
    
    <form method="POST" action="<?= APP_URL ?>/public/pedidos/guardar">
        <div class="form-group">
            <label>Repuesto *</label>
            <select name="repuesto_id" id="selectRepuesto" required onchange="mostrarInfoRepuesto()">
                <option value="">Seleccionar repuesto...</option>
                <?php if (!empty($repuestos)): ?>
                    <?php foreach ($repuestos as $r): ?>
                    <?php $disponible = max(0, ($r['stock'] ?? 0) - ($r['stock_reservado'] ?? 0)); ?>
                    <?php if ($disponible > 0): ?>
                    <option value="<?= $r['id'] ?>" 
                            data-stock="<?= $disponible ?>" 
                            data-categoria="<?= htmlspecialchars($r['categoria'] ?? '') ?>"
                            data-marca="<?= htmlspecialchars($r['marca'] ?? '') ?>"
                            data-precio="<?= $r['precio_unitario'] ?? 0 ?>">
                        <?= htmlspecialchars($r['nombre']) ?> - <?= htmlspecialchars($r['marca'] ?? 'Sin marca') ?> (Disp: <?= $disponible ?>)
                    </option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <div id="infoRepuesto" class="repuesto-info">
                <span id="infoRepuestoTexto"></span>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Cantidad *</label>
                <input type="number" name="cantidad" id="inputCantidad" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label>Sucursal destino</label>
                <select name="sucursal_id" required>
                    <?php if (!empty($sucursales)): ?>
                        <?php foreach ($sucursales as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == ($_SESSION['sucursal_id'] ?? '') ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Descripcion / Motivo</label>
            <textarea name="descripcion" rows="3" placeholder="Describe brevemente para que necesitas este repuesto..." style="width: 100%; padding: 10px; border: 2px solid var(--gris-claro); border-radius: 8px; font-family: inherit; font-size: 0.95rem; resize: vertical;"></textarea>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Enviar Pedido</button>
            <a href="<?= APP_URL ?>/public/pedidos" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

<script>
function mostrarInfoRepuesto() {
    var select = document.getElementById('selectRepuesto');
    var option = select.options[select.selectedIndex];
    var infoDiv = document.getElementById('infoRepuesto');
    var infoTexto = document.getElementById('infoRepuestoTexto');
    
    if (!option.value) {
        infoDiv.classList.remove('visible');
        return;
    }
    
    var stock = parseInt(option.getAttribute('data-stock')) || 0;
    var categoria = option.getAttribute('data-categoria') || '';
    var marca = option.getAttribute('data-marca') || '';
    var precio = parseFloat(option.getAttribute('data-precio')) || 0;
    
    var stockClass = stock > 0 ? 'stock-ok' : 'stock-bajo';
    var stockTexto = stock > 0 ? 'Stock disponible: ' + stock : 'Sin stock';
    
    infoTexto.innerHTML = '<strong>' + marca + '</strong> | ' + categoria + ' | <span class="' + stockClass + '">' + stockTexto + '</span> | S/ ' + precio.toFixed(2);
    infoDiv.classList.add('visible');
}
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
