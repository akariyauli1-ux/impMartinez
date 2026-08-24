<?php $titulo = 'Registrar Cliente y Equipo'; ob_start(); ?>

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
            <label>Cliente *</label>
            <select name="cliente_id" id="cliente_id_select">
                <option value="">Seleccionar cliente...</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre_completo']) ?> - <?= htmlspecialchars($cliente['telefono']) ?></option>
                <?php endforeach; ?>
            </select>
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

<form method="POST" action="<?= APP_URL ?>/public/recepcion/guardar-equipo" enctype="multipart/form-data" id="form_equipo">
    <div class="card">
        <div class="card-header">
            <h2>Datos del Equipo</h2>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de Equipo *</label>
                <select name="tipo_equipo" required>
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
                <input type="text" name="marca">
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
            <label>Fotos del Equipo</label>
            <input type="file" name="fotos[]" multiple accept="image/*">
            <small>Puedes seleccionar múltiples fotos</small>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Registrar Cliente y Equipo</button>
</form>

<script>
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
    const option = document.querySelector('input[name="cliente_option"]:checked').value;
    
    if (option === 'existente') {
        const clienteId = document.getElementById('cliente_id_select').value;
        if (!clienteId) {
            e.preventDefault();
            alert('Por favor selecciona un cliente existente');
            return false;
        }
    } else {
        const nombre = document.getElementById('cliente_nombre').value;
        const apellido = document.getElementById('cliente_apellido_paterno').value;
        const telefono = document.getElementById('cliente_telefono').value;
        
        if (!nombre || !apellido || !telefono) {
            e.preventDefault();
            alert('Por favor completa los datos obligatorios del nuevo cliente');
            return false;
        }
    }
});
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
