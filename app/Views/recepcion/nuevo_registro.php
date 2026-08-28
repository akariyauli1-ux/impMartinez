<?php $titulo = 'Registrar Cliente y Equipo'; ob_start(); ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'cliente_no_seleccionado'): ?>
<div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <strong>Error:</strong> Debe seleccionar un cliente existente o registrar un nuevo cliente.
</div>
<?php endif; ?>

<form method="POST" action="<?= APP_URL ?>/public/recepcion/guardar-equipo" enctype="multipart/form-data" id="form_equipo">
<div class="card">
    <div class="card-header">
        <h2>Datos del Cliente</h2>
    </div>
    
    <div class="form-group">
        <label>
            <input type="radio" name="cliente_option" value="existente" checked onchange="toggleClienteForm()"> 
            Seleccionar cliente existente
        </label>
        <label style="margin-left: 20px;">
            <input type="radio" name="cliente_option" value="nuevo" onchange="toggleClienteForm()"> 
            Registrar nuevo cliente
        </label>
    </div>
    
    <div id="cliente_existente_section">
        <div class="form-group">
            <label>Buscar Cliente (Nombre, Teléfono o DNI)</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" id="buscar_cliente" placeholder="Escribe para buscar..." style="flex: 1;">
                <button type="button" class="btn btn-primary" onclick="filtrarClientes()">🔍 Buscar</button>
                <button type="button" class="btn btn-outline" onclick="limpiarBusquedaCliente()">✖ Limpiar</button>
            </div>
        </div>
        <div class="form-group">
            <label>Cliente *</label>
            <select name="cliente_id" id="cliente_id_select" required onchange="mostrarDatosCliente()">
                <option value="">Seleccionar cliente...</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id'] ?>" 
                        data-nombre="<?= strtolower(htmlspecialchars($cliente['nombre_completo'])) ?>"
                        data-telefono="<?= htmlspecialchars($cliente['telefono']) ?>"
                        data-dni="<?= htmlspecialchars($cliente['dni'] ?? '') ?>"
                        data-email="<?= htmlspecialchars($cliente['email'] ?? '') ?>"
                        data-direccion="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
                        <?= htmlspecialchars($cliente['nombre_completo']) ?> - DNI: <?= htmlspecialchars($cliente['dni'] ?? 'N/A') ?> - Tel: <?= htmlspecialchars($cliente['telefono']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Sección para mostrar datos completos del cliente -->
        <div id="datos_cliente_completo" style="display: none; margin-top: 15px; padding: 15px; background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px;">
            <h4 style="margin: 0 0 10px 0; color: #2e7d32;">📋 Datos del Cliente Seleccionado</h4>
            <div id="contenido_datos_cliente"></div>
        </div>
    </div>
    
    <div id="cliente_nuevo_section" style="display: none;">
        <div class="form-row">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="cliente_nombre" id="cliente_nombre">
            </div>
            <div class="form-group">
                <label>Apellido Paterno *</label>
                <input type="text" name="cliente_apellido_paterno" id="cliente_apellido_paterno">
            </div>
            <div class="form-group">
                <label>Apellido Materno</label>
                <input type="text" name="cliente_apellido_materno" id="cliente_apellido_materno">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>DNI</label>
                <input type="text" name="cliente_dni" id="cliente_dni" maxlength="8">
            </div>
            <div class="form-group">
                <label>Teléfono *</label>
                <input type="tel" name="cliente_telefono" id="cliente_telefono">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="cliente_email" id="cliente_email">
            </div>
        </div>
        
        <div class="form-group">
            <label>Dirección</label>
            <textarea name="cliente_direccion" id="cliente_direccion" rows="2"></textarea>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Datos del Equipo</h2>
    </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de Equipo *</label>
                <select name="tipo_equipo" id="tipo_equipo_select" required onchange="actualizarMarcas()">
                    <option value="">Seleccionar...</option>
                    <option value="celular">Celular</option>
                    <option value="laptop">Laptop</option>
                    <option value="pc">PC</option>
                    <option value="tv">TV</option>
                    <option value="radio">Radio</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Marca</label>
                <select name="marca" id="marca_select" onchange="toggleMarcaOtra()">
                    <option value="">Seleccionar tipo de equipo primero...</option>
                </select>
                <input type="text" name="marca" id="marca_input" style="display: none;" placeholder="Escribir marca...">
            </div>
            <div class="form-group" id="marca_otra_group" style="display: none;">
                <label>Especificar Marca</label>
                <input type="text" name="marca_otra" id="marca_otra_input" placeholder="Escribir marca...">
            </div>
            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo">
            </div>
        </div>
        
        <div class="form-group">
            <label>Número de Serie</label>
            <input type="text" name="numero_serie">
        </div>
        
        <div class="form-group">
            <label>Accesorios</label>
            <textarea name="accesorios" rows="2" placeholder="Cargador, funda, etc."></textarea>
        </div>
        
        <div class="form-group">
            <label>Descripción de la Falla *</label>
            <textarea name="descripcion_falla" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Estado de Componentes</label>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f5f5f5;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Componente</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Buen Estado</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Mal Estado</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">No Aplica</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado de Pantalla</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_pantalla" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_pantalla" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_pantalla" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado de Carga</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_carga" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_carga" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_carga" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado de Puertos</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_puertos" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_puertos" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_puertos" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado de Case</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_case" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_case" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_case" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado Touch</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_touch" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_touch" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_touch" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado Cámara</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_camara" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_camara" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_camara" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado de Encendido</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_encendido" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_encendido" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_encendido" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Marco Doblado</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="marco_doblado" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="marco_doblado" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="marco_doblado" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado de Parlantes/Audio</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_parlantes" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_parlantes" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_parlantes" value="no_aplica">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">Estado de Imágenes/Rayaduras o Manchas</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_imagenes" value="buen_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_imagenes" value="mal_estado">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="estado_imagenes" value="no_aplica">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="form-group">
            <label>Estado Físico del Equipo</label>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f5f5f5;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Pregunta</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Sí</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">No</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">No Sabe</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">¿Está previamente abierto?</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="previamente_abierto" value="si">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="previamente_abierto" value="no">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="previamente_abierto" value="no_sabe">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">¿Contacto con líquidos / Entró al agua?</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="contacto_liquidos" value="si">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="contacto_liquidos" value="no">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="contacto_liquidos" value="no_sabe">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">¿Equipo reacondicionado / Adquirido a medio uso?</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="equipo_reacondicionado" value="si">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="equipo_reacondicionado" value="no">
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            <input type="radio" name="equipo_reacondicionado" value="no_sabe">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="form-group">
            <label>Foto del Anverso (Frente)</label>
            <input type="file" name="foto_anverso" accept="image/*">
            <small>Sube una foto de la parte frontal del equipo</small>
        </div>
        
        <div class="form-group">
            <label>Foto del Reverso (Parte Trasera)</label>
            <input type="file" name="foto_reverso" accept="image/*">
            <small>Sube una foto de la parte trasera del equipo</small>
        </div>
    </div>
    
    <button type="button" class="btn btn-primary" style="margin-top: 20px;" onclick="mostrarConfirmacion()">Generar Orden de Servicio</button>
</form>

<!-- Modal de Confirmación -->
<div id="modalConfirmacion" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2>Orden de Servicio - Confirmación</h2>
            <button class="modal-close" onclick="cerrarModalConfirmacion()">×</button>
        </div>
        <div style="padding: 20px;">
            <div id="resumenDatos"></div>
            
            <div style="margin-top: 20px; padding: 15px; background: #f0f8ff; border-left: 4px solid #007bff; border-radius: 4px;">
                <button type="button" class="btn btn-secondary" onclick="abrirModalInventario()" style="width: 100%;">
                    📦 Ver Inventario de Almacén (Consultar Precios)
                </button>
            </div>
            
            <div class="form-group" style="margin-top: 20px;">
                <label>Costo Estimado de Reparación (S/) *</label>
                <input type="number" id="costo_estimado" step="0.01" min="0" required style="font-size: 1.2em; font-weight: bold;">
            </div>
            
            <div class="form-group">
                <label>Observaciones Adicionales</label>
                <textarea id="observaciones" rows="3" placeholder="Notas adicionales sobre la reparación..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Firma del Cliente *</label>
                <canvas id="canvasFirma" width="600" height="200" style="border: 2px solid #ccc; cursor: crosshair; background: #fff;"></canvas>
                <div style="margin-top: 10px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="limpiarFirma()">Limpiar Firma</button>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-primary" onclick="confirmarRegistro()">Confirmar y Guardar</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalConfirmacion()">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Inventario -->
<div id="modalInventario" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 1000px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2>Inventario de Almacén</h2>
            <button class="modal-close" onclick="cerrarModalInventario()">×</button>
        </div>
        <div style="padding: 20px;">
            <div class="form-group">
                <label>Buscar Repuesto</label>
                <input type="text" id="buscarRepuesto" placeholder="Buscar por nombre, marca o categoría..." onkeyup="filtrarRepuestos()">
            </div>
            <div id="listaRepuestos"></div>
        </div>
    </div>
</div>

<button type="button" class="btn btn-secondary" style="margin-top: 10px;" onclick="abrirModalInventario()">Ver Inventario de Almacén</button>

<script>
const marcasPorTipo = {
    celular: ['Samsung', 'Apple', 'Xiaomi', 'Oppo', 'Vivo', 'Motorola', 'Honor', 'Realme', 'Huawei', 'Google', 'OnePlus', 'Tecno', 'Infinix', 'ZTE', 'Sony', 'Asus', 'Nothing'],
    laptop: ['Lenovo', 'HP', 'Dell', 'Apple', 'Asus', 'Acer', 'Microsoft', 'Samsung', 'MSI', 'Huawei', 'Gigabyte', 'Razer', 'Infinix', 'Tecno', 'Nothing'],
    pc: ['HP', 'Dell', 'Lenovo', 'Asus', 'MSI', 'Gigabyte', 'Corsair', 'Acer', 'Apple', 'NZXT', 'Thermaltake', 'Cooler Master', 'EVGA', 'Be Quiet', 'Lian Li'],
    tv: ['LG', 'Samsung', 'Sony', 'TCL', 'Hisense', 'Toshiba', 'Panasonic', 'Philips', 'Vizio', 'Sharp', 'Sceptre', 'Insignia', 'TCL', 'Hisense', 'Westinghouse'],
    radio: ['Sony', 'Panasonic', 'JBL', 'Bose', 'Samsung', 'LG', 'Philips', 'Pioneer', 'Kenwood', 'Alpine', 'Yamaha', 'Denon', 'Marantz', 'Onkyo', 'Harman Kardon']
};

function actualizarMarcas() {
    const tipoEquipo = document.getElementById('tipo_equipo_select').value;
    const marcaSelect = document.getElementById('marca_select');
    const marcaInput = document.getElementById('marca_input');
    const marcaOtraGroup = document.getElementById('marca_otra_group');
    
    // Limpiar opciones actuales
    marcaSelect.innerHTML = '';
    marcaOtraGroup.style.display = 'none';
    document.getElementById('marca_otra_input').value = '';
    
    if (!tipoEquipo || tipoEquipo === 'otro') {
        // Si es "otro" o no hay selección, mostrar campo de texto directamente
        marcaSelect.style.display = 'none';
        marcaInput.style.display = 'block';
        marcaInput.required = false;
    } else {
        // Mostrar select con marcas específicas
        marcaSelect.style.display = 'block';
        marcaInput.style.display = 'none';
        marcaInput.value = '';
        
        const optionDefault = document.createElement('option');
        optionDefault.value = '';
        optionDefault.textContent = 'Seleccionar marca...';
        marcaSelect.appendChild(optionDefault);
        
        const marcas = marcasPorTipo[tipoEquipo] || [];
        marcas.forEach(marca => {
            const option = document.createElement('option');
            option.value = marca;
            option.textContent = marca;
            marcaSelect.appendChild(option);
        });
        
        // Agregar opción "Otra"
        const optionOtra = document.createElement('option');
        optionOtra.value = 'otra';
        optionOtra.textContent = 'Otra (escribir manualmente)';
        marcaSelect.appendChild(optionOtra);
    }
}

function filtrarClientes() {
    const busqueda = document.getElementById('buscar_cliente').value.toLowerCase();
    const select = document.getElementById('cliente_id_select');
    const opciones = select.getElementsByTagName('option');
    
    let encontrados = 0;
    for (let i = 1; i < opciones.length; i++) {
        const nombre = opciones[i].getAttribute('data-nombre') || '';
        const telefono = opciones[i].getAttribute('data-telefono') || '';
        const dni = opciones[i].getAttribute('data-dni') || '';
        
        if (nombre.includes(busqueda) || telefono.includes(busqueda) || dni.includes(busqueda)) {
            opciones[i].style.display = '';
            encontrados++;
        } else {
            opciones[i].style.display = 'none';
        }
    }
    
    // Mostrar mensaje si no se encontraron resultados
    if (encontrados === 0 && busqueda !== '') {
        alert('No se encontraron clientes con ese criterio de búsqueda');
    }
}

function limpiarBusquedaCliente() {
    document.getElementById('buscar_cliente').value = '';
    const select = document.getElementById('cliente_id_select');
    const opciones = select.getElementsByTagName('option');
    
    for (let i = 1; i < opciones.length; i++) {
        opciones[i].style.display = '';
    }
    
    // Ocultar datos del cliente
    document.getElementById('datos_cliente_completo').style.display = 'none';
}

function mostrarDatosCliente() {
    const select = document.getElementById('cliente_id_select');
    const selectedOption = select.options[select.selectedIndex];
    const datosSection = document.getElementById('datos_cliente_completo');
    const contenidoDatos = document.getElementById('contenido_datos_cliente');
    
    if (select.value === '') {
        datosSection.style.display = 'none';
        return;
    }
    
    const nombre = selectedOption.getAttribute('data-nombre') || '';
    const telefono = selectedOption.getAttribute('data-telefono') || '';
    const dni = selectedOption.getAttribute('data-dni') || '';
    const email = selectedOption.getAttribute('data-email') || '';
    const direccion = selectedOption.getAttribute('data-direccion') || '';
    
    let html = '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">';
    html += `<p><strong>Nombre Completo:</strong> ${nombre.charAt(0).toUpperCase() + nombre.slice(1)}</p>`;
    html += `<p><strong>DNI:</strong> ${dni || 'No registrado'}</p>`;
    html += `<p><strong>Teléfono:</strong> ${telefono}</p>`;
    html += `<p><strong>Email:</strong> ${email || 'No registrado'}</p>`;
    html += `<p style="grid-column: 1 / -1;"><strong>Dirección:</strong> ${direccion || 'No registrada'}</p>`;
    html += '</div>';
    
    contenidoDatos.innerHTML = html;
    datosSection.style.display = 'block';
}

function toggleMarcaOtra() {
    const marcaSelect = document.getElementById('marca_select');
    const marcaOtraGroup = document.getElementById('marca_otra_group');
    const marcaOtraInput = document.getElementById('marca_otra_input');
    
    if (marcaSelect.value === 'otra') {
        marcaOtraGroup.style.display = 'block';
        marcaOtraInput.required = true;
    } else {
        marcaOtraGroup.style.display = 'none';
        marcaOtraInput.required = false;
        marcaOtraInput.value = '';
    }
}

// Inicializar el estado de marcas al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarMarcas();
    toggleClienteForm();
});

function toggleClienteForm() {
    const option = document.querySelector('input[name="cliente_option"]:checked').value;
    const existenteSection = document.getElementById('cliente_existente_section');
    const nuevoSection = document.getElementById('cliente_nuevo_section');
    const clienteIdSelect = document.getElementById('cliente_id_select');
    
    if (option === 'existente') {
        existenteSection.style.display = 'block';
        nuevoSection.style.display = 'none';
        clienteIdSelect.required = true;
        
        // Deshabilitar campos de nuevo cliente
        document.getElementById('cliente_nombre').required = false;
        document.getElementById('cliente_apellido_paterno').required = false;
        document.getElementById('cliente_telefono').required = false;
    } else {
        existenteSection.style.display = 'none';
        nuevoSection.style.display = 'block';
        clienteIdSelect.required = false;
        
        // Habilitar campos de nuevo cliente
        document.getElementById('cliente_nombre').required = true;
        document.getElementById('cliente_apellido_paterno').required = true;
        document.getElementById('cliente_telefono').required = true;
    }
}

// Validación del formulario
document.getElementById('form_equipo').addEventListener('submit', function(e) {
    e.preventDefault();
    mostrarConfirmacion();
});

// Variables para firma digital
let canvas = document.getElementById('canvasFirma');
let ctx = canvas.getContext('2d');
let dibujando = false;

// Inicializar canvas de firma
function inicializarFirma() {
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    
    canvas.addEventListener('mousedown', iniciarDibujo);
    canvas.addEventListener('mousemove', dibujar);
    canvas.addEventListener('mouseup', detenerDibujo);
    canvas.addEventListener('mouseout', detenerDibujo);
    
    // Soporte táctil
    canvas.addEventListener('touchstart', function(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        iniciarDibujo({offsetX: touch.clientX - rect.left, offsetY: touch.clientY - rect.top});
    });
    
    canvas.addEventListener('touchmove', function(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        dibujar({offsetX: touch.clientX - rect.left, offsetY: touch.clientY - rect.top});
    });
    
    canvas.addEventListener('touchend', detenerDibujo);
}

function iniciarDibujo(e) {
    dibujando = true;
    ctx.beginPath();
    ctx.moveTo(e.offsetX, e.offsetY);
}

function dibujar(e) {
    if (!dibujando) return;
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
}

function detenerDibujo() {
    dibujando = false;
}

function limpiarFirma() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function mostrarConfirmacion() {
    // Validar formulario primero
    const form = document.getElementById('form_equipo');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Recopilar datos del formulario
    const datosCliente = recopilarDatosCliente();
    const datosEquipo = recopilarDatosEquipo();
    
    // Mostrar resumen
    const resumenHTML = `
        <div class="card" style="background: #f5f5f5; margin-bottom: 20px;">
            <div class="card-header">
                <h3>Datos del Cliente</h3>
            </div>
            <div style="padding: 15px;">
                ${datosCliente}
            </div>
        </div>
        
        <div class="card" style="background: #f5f5f5; margin-bottom: 20px;">
            <div class="card-header">
                <h3>Datos del Equipo</h3>
            </div>
            <div style="padding: 15px;">
                ${datosEquipo}
            </div>
        </div>
        
        <div class="card" style="background: #fff3cd; border-left: 4px solid #ffc107;">
            <div style="padding: 15px;">
                <strong>⚠️ Importante:</strong> Verifique que toda la información sea correcta antes de confirmar. 
                El cliente deberá firmar para autorizar el inicio de la reparación.
            </div>
        </div>
    `;
    
    document.getElementById('resumenDatos').innerHTML = resumenHTML;
    document.getElementById('modalConfirmacion').style.display = 'flex';
    
    // Inicializar firma digital
    setTimeout(inicializarFirma, 100);
}

function recopilarDatosCliente() {
    const option = document.querySelector('input[name="cliente_option"]:checked').value;
    
    if (option === 'existente') {
        const select = document.getElementById('cliente_id_select');
        const texto = select.options[select.selectedIndex].text;
        return `<p><strong>Cliente Existente:</strong> ${texto}</p>`;
    } else {
        const nombre = document.getElementById('cliente_nombre').value;
        const apellido = document.getElementById('cliente_apellido_paterno').value;
        const apellidoM = document.getElementById('cliente_apellido_materno').value;
        const dni = document.getElementById('cliente_dni').value;
        const telefono = document.getElementById('cliente_telefono').value;
        const email = document.getElementById('cliente_email').value;
        
        return `
            <p><strong>Nombre Completo:</strong> ${nombre} ${apellido} ${apellidoM}</p>
            <p><strong>DNI:</strong> ${dni || 'No especificado'}</p>
            <p><strong>Teléfono:</strong> ${telefono}</p>
            <p><strong>Email:</strong> ${email || 'No especificado'}</p>
        `;
    }
}

function recopilarDatosEquipo() {
    const tipoEquipo = document.getElementById('tipo_equipo_select');
    const tipoTexto = tipoEquipo.options[tipoEquipo.selectedIndex].text;
    
    const marcaSelect = document.getElementById('marca_select');
    const marcaInput = document.getElementById('marca_input');
    let marca = '';
    
    if (marcaSelect.style.display !== 'none') {
        marca = marcaSelect.value === 'otra' ? 
            document.getElementById('marca_otra_input').value : 
            marcaSelect.options[marcaSelect.selectedIndex].text;
    } else {
        marca = marcaInput.value;
    }
    
    const modelo = document.querySelector('input[name="modelo"]').value;
    const serie = document.querySelector('input[name="numero_serie"]').value;
    const accesorios = document.querySelector('textarea[name="accesorios"]').value;
    const falla = document.querySelector('textarea[name="descripcion_falla"]').value;
    
    // Recopilar estados
    const estados = recopilarEstados();
    
    return `
        <p><strong>Tipo de Equipo:</strong> ${tipoTexto}</p>
        <p><strong>Marca:</strong> ${marca}</p>
        <p><strong>Modelo:</strong> ${modelo || 'No especificado'}</p>
        <p><strong>Número de Serie:</strong> ${serie || 'No especificado'}</p>
        <p><strong>Accesorios:</strong> ${accesorios || 'Ninguno'}</p>
        <p><strong>Descripción de Falla:</strong> ${falla}</p>
        <hr style="margin: 15px 0;">
        <h4>Estado de Componentes:</h4>
        ${estados}
    `;
}

function recopilarEstados() {
    const componentes = [
        {name: 'estado_pantalla', label: 'Pantalla'},
        {name: 'estado_carga', label: 'Carga'},
        {name: 'estado_puertos', label: 'Puertos'},
        {name: 'estado_case', label: 'Case'},
        {name: 'estado_touch', label: 'Touch'},
        {name: 'estado_camara', label: 'Cámara'},
        {name: 'estado_encendido', label: 'Encendido'},
        {name: 'marco_doblado', label: 'Marco'},
        {name: 'estado_parlantes', label: 'Parlantes'},
        {name: 'estado_imagenes', label: 'Imágenes'}
    ];
    
    let html = '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">';
    
    componentes.forEach(comp => {
        const radios = document.querySelectorAll(`input[name="${comp.name}"]`);
        let valor = 'No especificado';
        
        radios.forEach(radio => {
            if (radio.checked) {
                valor = radio.value.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            }
        });
        
        html += `<p><strong>${comp.label}:</strong> ${valor}</p>`;
    });
    
    html += '</div>';
    
    // Estado físico
    const fisicos = [
        {name: 'previamente_abierto', label: 'Previamente abierto'},
        {name: 'contacto_liquidos', label: 'Contacto con líquidos'},
        {name: 'equipo_reacondicionado', label: 'Reacondicionado'}
    ];
    
    html += '<h4 style="margin-top: 15px;">Estado Físico:</h4>';
    html += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">';
    
    fisicos.forEach(fis => {
        const radios = document.querySelectorAll(`input[name="${fis.name}"]`);
        let valor = 'No especificado';
        
        radios.forEach(radio => {
            if (radio.checked) {
                valor = radio.value === 'si' ? 'Sí' : (radio.value === 'no' ? 'No' : 'No sabe');
            }
        });
        
        html += `<p><strong>${fis.label}:</strong> ${valor}</p>`;
    });
    
    html += '</div>';
    
    return html;
}

function cerrarModalConfirmacion() {
    document.getElementById('modalConfirmacion').style.display = 'none';
    limpiarFirma();
}

function confirmarRegistro() {
    const costo = document.getElementById('costo_estimado').value;
    const observaciones = document.getElementById('observaciones').value;
    
    if (!costo || costo <= 0) {
        alert('Por favor ingrese un costo estimado de reparación');
        return;
    }
    
    // Verificar firma
    const canvasData = canvas.toDataURL();
    const blankCanvas = document.createElement('canvas');
    blankCanvas.width = canvas.width;
    blankCanvas.height = canvas.height;
    
    if (canvasData === blankCanvas.toDataURL()) {
        alert('Por favor el cliente debe firmar la orden de servicio');
        return;
    }
    
    // Crear campos ocultos para enviar al servidor
    const form = document.getElementById('form_equipo');
    
    // Agregar costo estimado
    let inputCosto = document.createElement('input');
    inputCosto.type = 'hidden';
    inputCosto.name = 'costo_estimado';
    inputCosto.value = costo;
    form.appendChild(inputCosto);
    
    // Agregar observaciones
    let inputObs = document.createElement('input');
    inputObs.type = 'hidden';
    inputObs.name = 'observaciones';
    inputObs.value = observaciones;
    form.appendChild(inputObs);
    
    // Agregar firma
    let inputFirma = document.createElement('input');
    inputFirma.type = 'hidden';
    inputFirma.name = 'firma_digital';
    inputFirma.value = canvasData;
    form.appendChild(inputFirma);
    
    // Enviar formulario
    form.submit();
}

function abrirModalInventario() {
    document.getElementById('modalInventario').style.display = 'flex';
    cargarRepuestos();
}

function cerrarModalInventario() {
    document.getElementById('modalInventario').style.display = 'none';
}

function cargarRepuestos() {
    fetch('<?= APP_URL ?>/public/consulta-almacen/obtener-repuestos')
        .then(response => response.json())
        .then(data => {
            mostrarRepuestos(data);
        })
        .catch(error => {
            console.error('Error al cargar repuestos:', error);
            document.getElementById('listaRepuestos').innerHTML = '<p style="color: red;">Error al cargar el inventario</p>';
        });
}

function mostrarRepuestos(repuestos) {
    if (repuestos.length === 0) {
        document.getElementById('listaRepuestos').innerHTML = '<p>No hay repuestos disponibles</p>';
        return;
    }
    
    let html = '<table style="width: 100%; border-collapse: collapse;">';
    html += '<thead><tr style="background: #f5f5f5;">';
    html += '<th style="padding: 10px; border: 1px solid #ddd;">Código</th>';
    html += '<th style="padding: 10px; border: 1px solid #ddd;">Nombre</th>';
    html += '<th style="padding: 10px; border: 1px solid #ddd;">Marca</th>';
    html += '<th style="padding: 10px; border: 1px solid #ddd;">Categoría</th>';
    html += '<th style="padding: 10px; border: 1px solid #ddd;">Stock</th>';
    html += '<th style="padding: 10px; border: 1px solid #ddd;">Precio Unit.</th>';
    html += '</tr></thead><tbody>';
    
    repuestos.forEach(rep => {
        html += `<tr>
            <td style="padding: 10px; border: 1px solid #ddd;">${rep.codigo || '-'}</td>
            <td style="padding: 10px; border: 1px solid #ddd;">${rep.nombre}</td>
            <td style="padding: 10px; border: 1px solid #ddd;">${rep.marca || '-'}</td>
            <td style="padding: 10px; border: 1px solid #ddd;">${rep.categoria || '-'}</td>
            <td style="padding: 10px; border: 1px solid #ddd;">${rep.stock}</td>
            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">S/ ${parseFloat(rep.precio_unitario).toFixed(2)}</td>
        </tr>`;
    });
    
    html += '</tbody></table>';
    document.getElementById('listaRepuestos').innerHTML = html;
}

function filtrarRepuestos() {
    const busqueda = document.getElementById('buscarRepuesto').value.toLowerCase();
    const filas = document.querySelectorAll('#listaRepuestos tbody tr');
    
    filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(busqueda) ? '' : 'none';
    });
}
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
