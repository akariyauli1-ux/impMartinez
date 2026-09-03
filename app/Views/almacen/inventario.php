<?php $titulo = 'Inventario de Repuestos'; ob_start(); ?>

<style>
.filtros-container {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    align-items: end;
}
.filtros-container .form-group {
    margin-bottom: 0;
    min-width: 200px;
}
.badge-descontinuado {
    background: #9e9e9e;
    color: white;
}
.badge-activo {
    background: #4caf50;
    color: white;
}
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= count($repuestos ?? []) ?></div>
        <div class="stat-label">Total Repuestos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($categorias ?? []) ?></div>
        <div class="stat-label">Categorias</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($marcas ?? []) ?></div>
        <div class="stat-label">Marcas</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Inventario de Repuestos</h2>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modalNuevo').classList.add('active')">+ Nuevo Repuesto</button>
    </div>
    
    <div class="filtros-container">
        <div class="form-group">
            <label>Filtrar por Categoria</label>
            <select id="filtroCategoria" onchange="filtrarTabla()">
                <option value="">Todas</option>
                <?php foreach ($categorias ?? [] as $cat): ?>
                <option value="<?= htmlspecialchars($cat['categoria']) ?>"><?= htmlspecialchars($cat['categoria']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Filtrar por Marca</label>
            <select id="filtroMarca" onchange="filtrarTabla()">
                <option value="">Todas</option>
                <?php foreach ($marcas ?? [] as $marca): ?>
                <option value="<?= htmlspecialchars($marca['marca']) ?>"><?= htmlspecialchars($marca['marca']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Gestionar Categorias</label>
            <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('modalEditarCategoria').classList.add('active')">Editar/Eliminar Categoria</button>
        </div>
    </div>
    
    <div class="table-container">
        <table id="tablaInventario">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Clave</th>
                    <th>Categoria</th>
                    <th>Stock</th>
                    <th>Reservado</th>
                    <th>Disp.</th>
                    <th>Precio</th>
                    <th>Solic.</th>
                    <th>Ventas</th>
                    <th>Inversion</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($repuestos)): ?>
                <tr>
                    <td colspan="14" style="text-align: center; padding: 20px;">No hay repuestos registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($repuestos as $r): ?>
                    <tr data-categoria="<?= htmlspecialchars($r['categoria'] ?? '') ?>" data-marca="<?= htmlspecialchars($r['marca'] ?? '') ?>">
                        <td><?= htmlspecialchars($r['codigo'] ?? '') ?></td>
                        <td><strong><?= htmlspecialchars($r['nombre'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($r['marca'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($r['clave_producto'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($r['categoria'] ?? '-') ?></td>
                        <td><?= $r['stock'] ?? 0 ?></td>
                        <td>
                            <?php $reservado = $r['stock_reservado'] ?? 0; ?>
                            <span style="<?= $reservado > 0 ? 'color: #E65100; font-weight: 600;' : '' ?>">
                                <?= $reservado ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            $disponibles = $r['unidades_disponibles'] ?? 0;
                            if ($disponibles <= 0) {
                                $color = '#C62828';
                                $texto = 'AGOTADO';
                            } elseif ($disponibles <= ($r['stock_minimo'] ?? 5)) {
                                $color = '#E65100';
                                $texto = $disponibles;
                            } else {
                                $color = '#2E7D32';
                                $texto = $disponibles;
                            }
                            ?>
                            <span style="color: <?= $color ?>; font-weight: 600;"><?= $texto ?></span>
                        </td>
                        <td>S/ <?= number_format($r['precio_unitario'] ?? 0, 2) ?></td>
                        <td><?= $r['solicitudes'] ?? 0 ?></td>
                        <td><?= $r['ventas'] ?? 0 ?></td>
                        <td>S/ <?= number_format($r['inversion'] ?? 0, 2) ?></td>
                        <td>
                            <?php if ($r['descontinuado'] ?? 0): ?>
                                <span class="badge badge-descontinuado">Descontinuado</span>
                            <?php elseif (($r['unidades_disponibles'] ?? 0) <= 0): ?>
                                <span class="badge badge-rojo">AGOTADO</span>
                            <?php elseif (($r['unidades_disponibles'] ?? 0) <= ($r['stock_minimo'] ?? 0)): ?>
                                <span class="badge badge-rojo">Stock Bajo</span>
                            <?php else: ?>
                                <span class="badge badge-activo">Activo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button type="button" class="btn btn-primary btn-sm" onclick="editarRepuesto(<?= htmlspecialchars(json_encode($r)) ?>)">Editar</button>
                                <form method="POST" action="<?= APP_URL ?>/public/almacen/toggle-descontinuado" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-outline btn-sm"><?= ($r['descontinuado'] ?? 0) ? 'Habilitar' : 'Deshabilitar' ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalNuevo" class="modal-overlay">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Nuevo Repuesto</h2>
            <button class="modal-close" onclick="cerrarModal()">x</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/almacen/guardar-repuesto">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Codigo *</label>
                    <input type="text" name="codigo" required placeholder="Ej: REP-001">
                </div>
                <div class="form-group">
                    <label>Clave del Producto</label>
                    <input type="text" name="clave_producto" placeholder="Ej: CLV-12345">
                </div>
            </div>
            <div class="form-group">
                <label>Nombre del Repuesto *</label>
                <input type="text" name="nombre" required placeholder="Ej: Pantalla iPhone 13">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Marca</label>
                    <input type="text" name="marca" placeholder="Ej: Apple, Samsung">
                </div>
                <div class="form-group">
                    <label>Categoria *</label>
                    <select name="categoria" id="selectCategoria" required onchange="mostrarNuevaCategoria()">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($todasCategorias as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                        <option value="__nueva__">+ Nueva categoria...</option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="nuevaCategoriaGroup" style="display: none;">
                <label>Nueva Categoria</label>
                <input type="text" name="nueva_categoria" id="inputNuevaCategoria" placeholder="Escribir nueva categoria">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Stock Actual</label>
                    <input type="number" name="stock" value="0" min="0">
                </div>
                <div class="form-group">
                    <label>Stock Minimo</label>
                    <input type="number" name="stock_minimo" value="5" min="0">
                </div>
                <div class="form-group">
                    <label>Precio Unitario (S/)</label>
                    <input type="number" name="precio_unitario" value="0" min="0" step="0.01">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="btn btn-primary">Guardar Repuesto</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditarRepuesto" class="modal-overlay">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Editar Repuesto</h2>
            <button class="modal-close" onclick="cerrarModalEditar()">x</button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/public/almacen/actualizar-repuesto">
            <input type="hidden" name="id" id="edit_id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Codigo *</label>
                    <input type="text" name="codigo" id="edit_codigo" required>
                </div>
                <div class="form-group">
                    <label>Clave del Producto</label>
                    <input type="text" name="clave_producto" id="edit_clave_producto">
                </div>
            </div>
            <div class="form-group">
                <label>Nombre del Repuesto *</label>
                <input type="text" name="nombre" id="edit_nombre" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Marca</label>
                    <input type="text" name="marca" id="edit_marca">
                </div>
                <div class="form-group">
                    <label>Categoria *</label>
                    <select name="categoria" id="edit_categoria" required onchange="mostrarNuevaCategoriaEditar()">
                        <option value="">Seleccionar...</option>
                        <?php 
                        foreach ($todasCategorias as $cat): 
                        ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                        <option value="__nueva__">+ Nueva categoria...</option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="nuevaCategoriaGroupEditar" style="display: none;">
                <label>Nueva Categoria</label>
                <input type="text" name="nueva_categoria" id="inputNuevaCategoriaEditar" placeholder="Escribir nueva categoria">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Stock Actual</label>
                    <input type="number" name="stock" id="edit_stock" min="0">
                </div>
                <div class="form-group">
                    <label>Stock Minimo</label>
                    <input type="number" name="stock_minimo" id="edit_stock_minimo" min="0">
                </div>
                <div class="form-group">
                    <label>Precio Unitario (S/)</label>
                    <input type="number" name="precio_unitario" id="edit_precio_unitario" min="0" step="0.01">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="btn btn-primary">Actualizar Repuesto</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalEditar()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditarCategoria" class="modal-overlay">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Gestionar Categorias</h2>
            <button class="modal-close" onclick="cerrarModalCategoria()">x</button>
        </div>
        <div style="padding: 20px;">
            <div class="form-group">
                <label>Seleccionar Categoria</label>
                <select id="selectCategoriaGestion" onchange="actualizarBotonesCategoria()">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($categorias ?? [] as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['categoria']) ?>"><?= htmlspecialchars($cat['categoria']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="accionesCategoria" style="display: none; margin-top: 20px;">
                <div class="form-group">
                    <label>Nuevo Nombre (para editar)</label>
                    <input type="text" id="nuevoNombreCategoria" placeholder="Nuevo nombre de categoria">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="button" class="btn btn-primary" onclick="editarCategoria()">Editar Categoria</button>
                    <button type="button" class="btn btn-danger" onclick="eliminarCategoria()">Eliminar Categoria</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarNuevaCategoria() {
    var select = document.getElementById('selectCategoria');
    var nuevaCategoriaGroup = document.getElementById('nuevaCategoriaGroup');
    
    if (select.value === '__nueva__') {
        nuevaCategoriaGroup.style.display = 'block';
        document.getElementById('inputNuevaCategoria').required = true;
    } else {
        nuevaCategoriaGroup.style.display = 'none';
        document.getElementById('inputNuevaCategoria').required = false;
    }
}

function filtrarTabla() {
    var categoria = document.getElementById('filtroCategoria').value;
    var marca = document.getElementById('filtroMarca').value;
    var filas = document.querySelectorAll('#tablaInventario tbody tr');
    
    filas.forEach(function(fila) {
        var catFila = fila.getAttribute('data-categoria');
        var marcaFila = fila.getAttribute('data-marca');
        
        var mostrarCategoria = !categoria || catFila === categoria;
        var mostrarMarca = !marca || marcaFila === marca;
        
        fila.style.display = (mostrarCategoria && mostrarMarca) ? '' : 'none';
    });
}

function cerrarModal() {
    document.getElementById('modalNuevo').classList.remove('active');
    document.getElementById('nuevaCategoriaGroup').style.display = 'none';
    document.getElementById('inputNuevaCategoria').required = false;
}

document.getElementById('modalNuevo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

function editarRepuesto(repuesto) {
    document.getElementById('edit_id').value = repuesto.id;
    document.getElementById('edit_codigo').value = repuesto.codigo || '';
    document.getElementById('edit_clave_producto').value = repuesto.clave_producto || '';
    document.getElementById('edit_nombre').value = repuesto.nombre || '';
    document.getElementById('edit_marca').value = repuesto.marca || '';
    document.getElementById('edit_categoria').value = repuesto.categoria || '';
    document.getElementById('edit_stock').value = repuesto.stock || 0;
    document.getElementById('edit_stock_minimo').value = repuesto.stock_minimo || 5;
    document.getElementById('edit_precio_unitario').value = repuesto.precio_unitario || 0;
    
    document.getElementById('modalEditarRepuesto').classList.add('active');
}

function mostrarNuevaCategoriaEditar() {
    var select = document.getElementById('edit_categoria');
    var nuevaCategoriaGroup = document.getElementById('nuevaCategoriaGroupEditar');
    
    if (select.value === '__nueva__') {
        nuevaCategoriaGroup.style.display = 'block';
        document.getElementById('inputNuevaCategoriaEditar').required = true;
    } else {
        nuevaCategoriaGroup.style.display = 'none';
        document.getElementById('inputNuevaCategoriaEditar').required = false;
    }
}

function cerrarModalEditar() {
    document.getElementById('modalEditarRepuesto').classList.remove('active');
    document.getElementById('nuevaCategoriaGroupEditar').style.display = 'none';
    document.getElementById('inputNuevaCategoriaEditar').required = false;
}

document.getElementById('modalEditarRepuesto').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalEditar();
});

function actualizarBotonesCategoria() {
    var select = document.getElementById('selectCategoriaGestion');
    var acciones = document.getElementById('accionesCategoria');
    
    if (select.value) {
        acciones.style.display = 'block';
        document.getElementById('nuevoNombreCategoria').value = select.value;
    } else {
        acciones.style.display = 'none';
    }
}

function editarCategoria() {
    var categoriaActual = document.getElementById('selectCategoriaGestion').value;
    var categoriaNueva = document.getElementById('nuevoNombreCategoria').value;
    
    if (!categoriaActual || !categoriaNueva) {
        alert('Por favor completa todos los campos');
        return;
    }
    
    if (categoriaActual === categoriaNueva) {
        alert('El nuevo nombre debe ser diferente al actual');
        return;
    }
    
    if (confirm('¿Editar categoria "' + categoriaActual + '" a "' + categoriaNueva + '"?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= APP_URL ?>/public/almacen/editar-categoria';
        
        var input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'categoria_actual';
        input1.value = categoriaActual;
        form.appendChild(input1);
        
        var input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'categoria_nueva';
        input2.value = categoriaNueva;
        form.appendChild(input2);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function eliminarCategoria() {
    var categoria = document.getElementById('selectCategoriaGestion').value;
    
    if (!categoria) {
        alert('Selecciona una categoria');
        return;
    }
    
    if (confirm('¿Eliminar categoria "' + categoria + '"? Los repuestos con esta categoria quedaran sin categoria asignada.')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= APP_URL ?>/public/almacen/eliminar-categoria';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'categoria';
        input.value = categoria;
        form.appendChild(input);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function cerrarModalCategoria() {
    document.getElementById('modalEditarCategoria').classList.remove('active');
    document.getElementById('selectCategoriaGestion').value = '';
    document.getElementById('accionesCategoria').style.display = 'none';
}

document.getElementById('modalEditarCategoria').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalCategoria();
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
