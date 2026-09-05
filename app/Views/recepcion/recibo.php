<?php $titulo = 'Recibo de Servicio'; ob_start(); ?>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #recibo, #recibo * {
        visibility: visible;
    }
    #recibo {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .page-break {
        page-break-after: always;
    }
}

#recibo {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 30px;
    background: white;
    border: 1px solid #ddd;
}

.recibo-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #333;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.recibo-header img {
    max-width: 100px;
    max-height: 80px;
    margin-bottom: 0;
}

.recibo-header h1 {
    margin: 0 0 5px 0;
    font-size: 20px;
    color: #333;
}

.recibo-header p {
    margin: 2px 0;
    font-size: 13px;
    color: #666;
}

.recibo-numero {
    background: #f5f5f5;
    padding: 10px;
    margin: 15px 0;
    border-left: 4px solid #333;
    font-size: 16px;
}

.seccion {
    margin: 20px 0;
    padding: 15px;
    background: #f9f9f9;
    border-left: 3px solid #333;
}

.seccion h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: #333;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.seccion p {
    margin: 5px 0;
    font-size: 14px;
}

.seccion strong {
    color: #333;
}

.datos-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin: 10px 0;
}

.dato-item {
    margin: 5px 0;
}

.firma-container {
    margin: 15px 0;
    text-align: center;
}

.firma-container img {
    max-width: 200px;
    border: 1px solid #ddd;
    padding: 5px;
    background: white;
}

.firma-linea {
    margin-top: 5px;
    border-top: 1px solid #333;
    width: 200px;
    margin-left: auto;
    margin-right: auto;
    padding-top: 3px;
    font-size: 10px;
}

.costo-total {
    background: #e8f5e9;
    padding: 15px;
    margin: 20px 0;
    border: 2px solid #4caf50;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    color: #2e7d32;
}

.observaciones {
    background: #fff3e0;
    padding: 15px;
    margin: 20px 0;
    border-left: 4px solid #ff9800;
}

.observaciones h4 {
    margin: 0 0 10px 0;
    color: #e65100;
}

.estado-componente {
    display: inline-block;
    padding: 3px 8px;
    margin: 2px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
}

.estado-bueno {
    background: #c8e6c9;
    color: #2e7d32;
}

.estado-malo {
    background: #ffcdd2;
    color: #c62828;
}

.estado-no-aplica {
    background: #e0e0e0;
    color: #616161;
}

.botones-accion {
    text-align: center;
    margin: 20px 0;
}

.btn-imprimir {
    background: #2196f3;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin: 5px;
}

.btn-imprimir:hover {
    background: #1976d2;
}

.btn-volver {
    background: #757575;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin: 5px;
    text-decoration: none;
    display: inline-block;
}

.btn-volver:hover {
    background: #616161;
}

.footer-recibo {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    text-align: center;
    font-size: 12px;
    color: #666;
}
</style>

<div class="no-print" style="text-align: center; margin: 20px 0;">
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Recibo</button>
    <a href="<?= APP_URL ?>/public/recepcion/nuevo-equipo" class="btn-volver">← Registrar Otro Equipo</a>
</div>

<div id="recibo">
    <!-- Cabecera del Recibo -->
    <div class="recibo-header" style="display: flex; justify-content: space-between; align-items: center; text-align: left;">
        <?php 
        $sucursalModel = new \Sucursal();
        $logo_data = $sucursalModel->obtenerLogoEmpresa();
        if ($logo_data && file_exists(__DIR__ . '/../../../uploads/logos/' . $logo_data)): 
        ?>
            <img src="<?= APP_URL ?>/uploads/logos/<?= $logo_data ?>" alt="Logo Empresa" style="max-width: 100px; max-height: 80px;">
        <?php endif; ?>
        
        <div style="text-align: right;">
            <h1><?= htmlspecialchars(APP_NAME) ?></h1>
            <p><strong><?= htmlspecialchars($sucursal['nombre']) ?></strong></p>
            <p><?= htmlspecialchars($sucursal['direccion']) ?></p>
            <p>Tel: <?= htmlspecialchars($sucursal['telefono']) ?></p>
        </div>
    </div>
    
    <!-- Número de Orden -->
    <div class="recibo-numero">
        <strong>N° de Orden:</strong> ORD-<?= str_pad($equipo['id'], 6, '0', STR_PAD_LEFT) ?>
        <span style="float: right;">
            <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?>
        </span>
    </div>
    
    <?php if (!empty($equipo['fecha_estimada_entrega'])): ?>
    <div style="background: #e3f2fd; padding: 10px; margin: 10px 0; border-left: 4px solid #2196f3; border-radius: 4px;">
        <strong>📅 Fecha Estimada de Entrega:</strong> <?= date('d/m/Y', strtotime($equipo['fecha_estimada_entrega'])) ?>
    </div>
    <?php endif; ?>
    
    <!-- Datos del Cliente y Equipo en columnas -->
    <div style="display: flex; gap: 15px; margin: 15px 0;">
        <!-- Datos del Cliente (Izquierda) -->
        <div class="seccion" style="flex: 1; margin: 0;">
            <h3>👤 Datos del Cliente</h3>
            <div class="datos-grid">
                <div class="dato-item">
                    <strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno']) ?>
                </div>
                <div class="dato-item">
                    <strong>DNI:</strong> <?= htmlspecialchars($cliente['dni'] ?? 'No especificado') ?>
                </div>
                <div class="dato-item">
                    <strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono']) ?>
                </div>
                <div class="dato-item">
                    <strong>Email:</strong> <?= htmlspecialchars($cliente['email'] ?? 'No especificado') ?>
                </div>
                <div class="dato-item" style="grid-column: 1 / -1;">
                    <strong>Dirección:</strong> <?= htmlspecialchars($cliente['direccion'] ?? 'No especificada') ?>
                </div>
            </div>
        </div>
        
        <!-- Datos del Equipo (Derecha) -->
        <div class="seccion" style="flex: 1; margin: 0;">
            <h3>📱 Datos del Equipo</h3>
            <div class="datos-grid">
                <div class="dato-item">
                    <strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($equipo['tipo_equipo'])) ?>
                </div>
                <div class="dato-item">
                    <strong>Marca:</strong> <?= htmlspecialchars($equipo['marca']) ?>
                </div>
                <div class="dato-item">
                    <strong>Modelo:</strong> <?= htmlspecialchars($equipo['modelo'] ?? 'No especificado') ?>
                </div>
                <div class="dato-item">
                    <strong>N° Serie:</strong> <?= htmlspecialchars($equipo['numero_serie'] ?? 'No especificado') ?>
                </div>
                <div class="dato-item" style="grid-column: 1 / -1;">
                    <strong>Accesorios:</strong> <?= htmlspecialchars($equipo['accesorios'] ?? 'Ninguno') ?>
                </div>
                <div class="dato-item" style="grid-column: 1 / -1;">
                    <strong>Falla Reportada:</strong><br>
                    <?= htmlspecialchars($equipo['descripcion_falla']) ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fotos del Equipo -->
    <?php if (!empty($fotos)): ?>
    <div class="seccion">
        <h3>📸 Fotos del Equipo</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
            <?php foreach ($fotos as $foto): ?>
                <div style="text-align: center;">
                    <div style="width: 150px; height: 150px; margin: 0 auto; overflow: hidden; border: 1px solid #ddd; border-radius: 4px; background: #f5f5f5;">
                        <img src="<?= APP_URL ?>/public/imagen/foto-equipo?id=<?= $foto['id'] ?>" alt="Foto del equipo" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <p style="margin-top: 5px; font-size: 12px; color: #666;"><?= htmlspecialchars(ucfirst($foto['tipo'] ?? 'General')) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Estado de Componentes -->
    <div class="seccion">
        <h3>🔍 Estado de Componentes</h3>
        <?php
        $componentes = [
            'estado_pantalla' => 'Pantalla',
            'estado_carga' => 'Carga',
            'estado_puertos' => 'Puertos',
            'estado_case' => 'Case',
            'estado_touch' => 'Touch',
            'estado_camara' => 'Cámara',
            'estado_encendido' => 'Encendido',
            'marco_doblado' => 'Marco',
            'estado_parlantes' => 'Parlantes',
            'estado_imagenes' => 'Imágenes'
        ];
        
        foreach ($componentes as $campo => $nombre):
            $valor = $equipo[$campo] ?? null;
            if ($valor):
                $clase = '';
                $texto = '';
                if ($valor === 'buen_estado') {
                    $clase = 'estado-bueno';
                    $texto = 'Buen Estado';
                } elseif ($valor === 'mal_estado') {
                    $clase = 'estado-malo';
                    $texto = 'Mal Estado';
                } else {
                    $clase = 'estado-no-aplica';
                    $texto = 'No Aplica';
                }
        ?>
            <span class="estado-componente <?= $clase ?>">
                <?= $nombre ?>: <?= $texto ?>
            </span>
        <?php 
            endif;
        endforeach; 
        ?>
    </div>
    
    <!-- Estado Físico -->
    <div class="seccion">
        <h3>📋 Estado Físico</h3>
        <?php
        $fisicos = [
            'previamente_abierto' => 'Previamente abierto',
            'contacto_liquidos' => 'Contacto con líquidos',
            'equipo_reacondicionado' => 'Reacondicionado'
        ];
        
        foreach ($fisicos as $campo => $nombre):
            $valor = $equipo[$campo] ?? null;
            if ($valor):
                $texto = $valor === 'si' ? 'Sí' : ($valor === 'no' ? 'No' : 'No sabe');
        ?>
            <p><strong><?= $nombre ?>:</strong> <?= $texto ?></p>
        <?php 
            endif;
        endforeach; 
        ?>
    </div>
    
    <!-- Costo Estimado -->
    <?php if ($equipo['costo_estimado']): ?>
    <div class="costo-total">
        💰 COSTO ESTIMADO DE REPARACIÓN: S/ <?= number_format($equipo['costo_estimado'], 2) ?>
    </div>
    <?php endif; ?>
    
    <!-- Observaciones -->
    <?php if ($equipo['observaciones']): ?>
    <div class="observaciones">
        <h4>📝 Observaciones:</h4>
        <p><?= nl2br(htmlspecialchars($equipo['observaciones'])) ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Firma del Cliente -->
    <?php if ($equipo['firma_digital']): ?>
    <div class="firma-container" style="margin: 15px 0; text-align: center;">
        <h3 style="text-align: center; margin-bottom: 10px; font-size: 14px;">✍️ Firma de Conformidad del Cliente</h3>
        <img src="<?= $equipo['firma_digital'] ?>" alt="Firma del Cliente" style="max-width: 200px; border: 1px solid #ddd; padding: 5px; background: white;">
        <div class="firma-linea" style="margin-top: 5px; border-top: 1px solid #333; width: 200px; margin-left: auto; margin-right: auto; padding-top: 3px; font-size: 10px;">
            <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno']) ?><br>
            <small>DNI: <?= htmlspecialchars($cliente['dni'] ?? 'N/A') ?> | <?= date('d/m/Y H:i', strtotime($equipo['fecha_conformidad'])) ?></small>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Datos del Personal -->
    <div class="seccion">
        <h3>👥 Personal que Atiende</h3>
        <div class="datos-grid">
            <div class="dato-item">
                <strong>Recepcionista:</strong><br>
                <?= htmlspecialchars($recepcionista['nombre'] . ' ' . $recepcionista['apellido_paterno']) ?>
            </div>
            <div class="dato-item">
                <strong>Administrador de Sucursal:</strong><br>
                <?php if ($admin_sucursal): ?>
                    <?= htmlspecialchars($admin_sucursal['nombre'] . ' ' . $admin_sucursal['apellido_paterno']) ?>
                <?php else: ?>
                    No asignado
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer-recibo" style="position: relative; min-height: 100px;">
        <p>Este documento es un comprobante de recepción del equipo para reparación.</p>
        <p>El cliente autoriza el inicio de los trabajos de reparación según el costo estimado indicado.</p>
        <p><strong><?= htmlspecialchars(APP_NAME) ?></strong> - <?= htmlspecialchars($sucursal['nombre']) ?></p>
        <p>Generado el <?= date('d/m/Y H:i:s') ?></p>
        
        <?php 
        $hash_seguridad = $equipo['hash_seguridad'] ?? hash('sha256', $equipo['id'] . $equipo['fecha_registro'] . 'recepcion_impmartinez');
        $url_verificacion = APP_URL . '/public/recepcion/verificar-entrega?hash=' . urlencode($hash_seguridad);
        $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($url_verificacion) . '&color=1565c0&bgcolor=ffffff&margin=4';
        ?>
        <div style="position: absolute; right: 20px; bottom: 15px; text-align: center;">
            <img src="<?= $qr_url ?>" alt="QR Seguridad" style="border: 2px solid #1565c0; border-radius: 4px; width: 70px; height: 70px;">
            <p style="font-size: 8px; color: #1565c0; margin-top: 3px; font-weight: bold;">🔒 Verificar autenticidad</p>
        </div>
    </div>
</div>

<div class="no-print" style="text-align: center; margin: 20px 0;">
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Recibo</button>
    <button class="btn-imprimir" onclick="descargarPDF()" style="background: #dc3545;">📄 Descargar PDF</button>
    <button class="btn-imprimir" onclick="descargarImagen()" style="background: #28a745;">🖼️ Descargar Imagen</button>
    <a href="<?= APP_URL ?>/public/recepcion/nuevo-equipo" class="btn-volver">← Registrar Otro Equipo</a>
</div>

<!-- Librerías para generar PDF e Imagen -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
function descargarPDF() {
    const elemento = document.getElementById('recibo');
    const btnImprimir = document.querySelector('.no-print');
    
    // Ocultar botones temporalmente
    btnImprimir.style.display = 'none';
    
    // Mostrar mensaje de carga
    const loading = document.createElement('div');
    loading.id = 'loading';
    loading.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 20px 40px; border-radius: 10px; z-index: 9999; font-size: 18px;';
    loading.innerHTML = '⏳ Generando PDF...';
    document.body.appendChild(loading);
    
    html2canvas(elemento, {
        scale: 2,
        useCORS: true,
        logging: false,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        const pdfWidth = 210; // Ancho A4 en mm
        const pdfHeight = 297; // Alto A4 en mm
        const imgWidth = canvas.width;
        const imgHeight = canvas.height;
        
        // Calcular la escala para que todo quepa en una página
        const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
        const scaledWidth = imgWidth * ratio;
        const scaledHeight = imgHeight * ratio;
        
        // Centrar la imagen en la página
        const x = (pdfWidth - scaledWidth) / 2;
        const y = (pdfHeight - scaledHeight) / 2;
        
        pdf.addImage(imgData, 'PNG', x, y, scaledWidth, scaledHeight);
        
        const numeroOrden = 'ORD-<?= str_pad($equipo['id'], 6, '0', STR_PAD_LEFT) ?>';
        pdf.save('Recibo_' + numeroOrden + '.pdf');
        
        // Remover mensaje de carga y mostrar botones
        document.getElementById('loading').remove();
        btnImprimir.style.display = 'block';
    }).catch(error => {
        console.error('Error al generar PDF:', error);
        document.getElementById('loading').remove();
        btnImprimir.style.display = 'block';
        alert('Error al generar el PDF. Por favor intente nuevamente.');
    });
}

function descargarImagen() {
    const elemento = document.getElementById('recibo');
    const btnImprimir = document.querySelector('.no-print');
    
    // Ocultar botones temporalmente
    btnImprimir.style.display = 'none';
    
    // Mostrar mensaje de carga
    const loading = document.createElement('div');
    loading.id = 'loading';
    loading.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 20px 40px; border-radius: 10px; z-index: 9999; font-size: 18px;';
    loading.innerHTML = '⏳ Generando imagen...';
    document.body.appendChild(loading);
    
    html2canvas(elemento, {
        scale: 2,
        useCORS: true,
        logging: false,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const link = document.createElement('a');
        const numeroOrden = 'ORD-<?= str_pad($equipo['id'], 6, '0', STR_PAD_LEFT) ?>';
        link.download = 'Recibo_' + numeroOrden + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        
        // Remover mensaje de carga y mostrar botones
        document.getElementById('loading').remove();
        btnImprimir.style.display = 'block';
    }).catch(error => {
        console.error('Error al generar imagen:', error);
        document.getElementById('loading').remove();
        btnImprimir.style.display = 'block';
        alert('Error al generar la imagen. Por favor intente nuevamente.');
    });
}
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
