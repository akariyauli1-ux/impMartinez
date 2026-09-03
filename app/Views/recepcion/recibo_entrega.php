<?php $titulo = 'Recibo de Entrega'; ob_start(); ?>

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
    text-align: center;
    border-bottom: 3px solid #333;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.recibo-header img {
    max-width: 150px;
    max-height: 100px;
    margin-bottom: 10px;
}

.recibo-header h1 {
    margin: 10px 0 5px 0;
    font-size: 24px;
    color: #333;
}

.recibo-header p {
    margin: 3px 0;
    font-size: 14px;
    color: #666;
}

.recibo-numero {
    background: #e8f5e9;
    padding: 10px;
    margin: 15px 0;
    border-left: 4px solid #4caf50;
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

.footer-recibo {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    text-align: center;
    font-size: 12px;
    color: #666;
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
</style>

<div class="no-print" style="text-align: center; margin: 20px 0;">
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Recibo</button>
    <button class="btn-imprimir" onclick="descargarPDF()" style="background: #dc3545;">📄 Descargar PDF</button>
    <a href="<?= APP_URL ?>/public/recepcion/equipos-listos" class="btn-volver">← Volver a Equipos Listos</a>
</div>

<div id="recibo">
    <!-- Cabecera del Recibo -->
    <div class="recibo-header" style="display: flex; justify-content: space-between; align-items: center; text-align: left;">
        <div style="text-align: left;">
            <h1><?= htmlspecialchars(APP_NAME) ?></h1>
            <p><strong><?= htmlspecialchars($sucursal['nombre']) ?></strong></p>
            <p><?= htmlspecialchars($sucursal['direccion']) ?></p>
            <p>Tel: <?= htmlspecialchars($sucursal['telefono']) ?></p>
        </div>
        <?php 
        $sucursalModel = new \Sucursal();
        $logo_data = $sucursalModel->obtenerLogoEmpresa();
        if ($logo_data && file_exists(__DIR__ . '/../../../uploads/logos/' . $logo_data)): 
        ?>
            <img src="<?= APP_URL ?>/uploads/logos/<?= $logo_data ?>" alt="Logo Empresa" style="max-width: 100px; max-height: 80px;">
        <?php endif; ?>
    </div>
    
    <!-- Número de Orden -->
    <div class="recibo-numero">
        <strong>N° de Orden:</strong> ORD-<?= str_pad($equipo['id'], 6, '0', STR_PAD_LEFT) ?>
        <span style="float: right;">
            <strong>Fecha de Entrega:</strong> <?= date('d/m/Y H:i', strtotime($equipo['fecha_entrega'])) ?>
        </span>
    </div>
    
    <?php if (!empty($equipo['fecha_estimada_entrega'])): ?>
    <div style="background: #e3f2fd; padding: 10px; margin: 10px 0; border-left: 4px solid #2196f3; border-radius: 4px;">
        <strong>📅 Fecha Estimada de Entrega:</strong> <?= date('d/m/Y', strtotime($equipo['fecha_estimada_entrega'])) ?>
        <?php 
        $fecha_estimada = strtotime($equipo['fecha_estimada_entrega']);
        $fecha_entrega = strtotime($equipo['fecha_entrega']);
        $diferencia = floor(($fecha_entrega - $fecha_estimada) / (60 * 60 * 24));
        if ($diferencia > 0): ?>
            <span style="color: #d32f2f; margin-left: 10px;">(Entregado <?= $diferencia ?> día(s) después de la fecha estimada)</span>
        <?php elseif ($diferencia < 0): ?>
            <span style="color: #388e3c; margin-left: 10px;">(Entregado <?= abs($diferencia) ?> día(s) antes de la fecha estimada)</span>
        <?php else: ?>
            <span style="color: #388e3c; margin-left: 10px;">(Entregado en la fecha estimada)</span>
        <?php endif; ?>
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
                    <strong>Falla Reportada:</strong><br>
                    <?= htmlspecialchars($equipo['descripcion_falla']) ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fotos del Equipo -->
    <?php if (!empty($fotos)): ?>
    <div class="seccion">
        <h3>📸 Fotos del Equipo al Recepcionar</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
            <?php foreach ($fotos as $foto): ?>
                <div style="text-align: center;">
                    <img src="<?= APP_URL ?>/public/imagen/foto-equipo?id=<?= $foto['id'] ?>" alt="Foto del equipo" style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                    <p style="margin-top: 5px; font-size: 12px; color: #666;"><?= htmlspecialchars(ucfirst($foto['tipo'] ?? 'General')) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Componentes Usados -->
    <?php if (!empty($componentes)): ?>
    <div class="seccion" style="background: #e8f5e9; border-left-color: #4caf50;">
        <h3>🔧 Componentes Usados en la Reparación</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px;">
            <thead>
                <tr style="background: #c8e6c9;">
                    <th style="padding: 8px; text-align: left; border: 1px solid #a5d6a7;">Componente</th>
                    <th style="padding: 8px; text-align: center; border: 1px solid #a5d6a7;">Cant.</th>
                    <th style="padding: 8px; text-align: right; border: 1px solid #a5d6a7;">P. Unit.</th>
                    <th style="padding: 8px; text-align: right; border: 1px solid #a5d6a7;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($componentes as $comp): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <strong><?= htmlspecialchars($comp['repuesto_nombre']) ?></strong>
                        <?php if (!empty($comp['repuesto_codigo'])): ?>
                            <br><small style="color: #666;">Cod: <?= htmlspecialchars($comp['repuesto_codigo']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 8px; text-align: center; border: 1px solid #ddd;"><?= $comp['cantidad'] ?></td>
                    <td style="padding: 8px; text-align: right; border: 1px solid #ddd;">S/ <?= number_format($comp['precio_unitario'], 2) ?></td>
                    <td style="padding: 8px; text-align: right; border: 1px solid #ddd; font-weight: bold;">S/ <?= number_format($comp['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #c8e6c9; font-weight: bold;">
                    <td colspan="3" style="padding: 8px; text-align: right; border: 1px solid #a5d6a7;">Total Componentes:</td>
                    <td style="padding: 8px; text-align: right; border: 1px solid #a5d6a7;">S/ <?= number_format($equipo['costo_reparacion'] ?? 0, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Costo de Reparación -->
    <div class="costo-total">
        💰 COSTO FINAL DE REPARACIÓN: S/ <?= number_format($equipo['costo_final'], 2) ?>
    </div>
    
    <?php if ($equipo['costo_estimado'] && $equipo['costo_estimado'] != $equipo['costo_final']): ?>
    <div class="seccion" style="background: #fff3e0; border-left-color: #ff9800;">
        <p><strong>Costo Estimado Original:</strong> S/ <?= number_format($equipo['costo_estimado'], 2) ?></p>
        <?php if (!empty($componentes)): ?>
        <p><strong>Costo de Componentes:</strong> S/ <?= number_format($equipo['costo_reparacion'] ?? 0, 2) ?></p>
        <?php endif; ?>
        <p><strong>Diferencia:</strong> S/ <?= number_format($equipo['costo_final'] - $equipo['costo_estimado'], 2) ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Firmas lado a lado -->
    <div style="display: flex; gap: 20px; margin: 15px 0;">
        <!-- Firma de Recepción (Izquierda) -->
        <?php if ($equipo['firma_digital']): ?>
        <div class="firma-container" style="flex: 1;">
            <h3 style="text-align: center; margin-bottom: 10px; color: #2196f3; font-size: 14px;">✍️ Firma de Recepción</h3>
            <img src="<?= $equipo['firma_digital'] ?>" alt="Firma de Recepción">
            <div class="firma-linea">
                <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno']) ?><br>
                <small><?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></small>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Firma de Conformidad (Derecha) -->
        <?php if ($equipo['firma_entrega']): ?>
        <div class="firma-container" style="flex: 1;">
            <h3 style="text-align: center; margin-bottom: 10px; color: #4caf50; font-size: 14px;">✍️ Firma de Conformidad</h3>
            <img src="<?= $equipo['firma_entrega'] ?>" alt="Firma de Entrega">
            <div class="firma-linea">
                <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno']) ?><br>
                <small>DNI: <?= htmlspecialchars($cliente['dni'] ?? 'N/A') ?> | <?= date('d/m/Y H:i', strtotime($equipo['fecha_entrega'])) ?></small>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Datos del Personal -->
    <div class="seccion">
        <h3>👥 Personal que Atiende</h3>
        <div class="datos-grid">
            <div class="dato-item">
                <strong>Entregado por:</strong><br>
                <?= htmlspecialchars($recepcionista['nombre'] . ' ' . $recepcionista['apellido_paterno']) ?>
            </div>
            <div class="dato-item">
                <strong>Rol:</strong><br>
                <?= htmlspecialchars(ucfirst($recepcionista['rol'])) ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer-recibo" style="position: relative; min-height: 100px;">
        <p>Este documento es un comprobante de entrega del equipo reparado.</p>
        <p>El cliente firma conforme con la reparación realizada y el costo final indicado.</p>
        <p><strong><?= htmlspecialchars(APP_NAME) ?></strong> - <?= htmlspecialchars($sucursal['nombre']) ?></p>
        <p>Generado el <?= date('d/m/Y H:i:s') ?></p>
        
        <?php if (!empty($equipo['hash_seguridad'])): ?>
        <?php 
        $url_verificacion = APP_URL . '/public/recepcion/verificar-entrega?hash=' . urlencode($equipo['hash_seguridad']);
        $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($url_verificacion) . '&color=9c27b0&bgcolor=ffffff&margin=4';
        ?>
        <div style="position: absolute; right: 20px; bottom: 15px; text-align: center;">
            <img src="<?= $qr_url ?>" alt="QR Seguridad" style="border: 2px solid #9c27b0; border-radius: 4px; width: 70px; height: 70px;">
            <p style="font-size: 8px; color: #9c27b0; margin-top: 3px; font-weight: bold;">🔒 Verificar autenticidad</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="no-print" style="text-align: center; margin: 20px 0;">
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Recibo</button>
    <button class="btn-imprimir" onclick="descargarPDF()" style="background: #dc3545;">📄 Descargar PDF</button>
    <a href="<?= APP_URL ?>/public/recepcion/equipos-listos" class="btn-volver">← Volver a Equipos Listos</a>
</div>

<!-- Librerías para generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
// Generar y descargar PDF automáticamente
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        descargarPDF();
    }, 1000);
});

function descargarPDF() {
    const elemento = document.getElementById('recibo');
    const btnImprimir = document.querySelector('.no-print');
    
    btnImprimir.style.display = 'none';
    
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
        
        const pdfWidth = 210;
        const pdfHeight = 297;
        const imgWidth = canvas.width;
        const imgHeight = canvas.height;
        
        const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
        const scaledWidth = imgWidth * ratio;
        const scaledHeight = imgHeight * ratio;
        
        const x = (pdfWidth - scaledWidth) / 2;
        const y = (pdfHeight - scaledHeight) / 2;
        
        pdf.addImage(imgData, 'PNG', x, y, scaledWidth, scaledHeight);
        
        const numeroOrden = 'ENTREGA-ORD-<?= str_pad($equipo['id'], 6, '0', STR_PAD_LEFT) ?>';
        pdf.save('Recibo_Entrega_' + numeroOrden + '.pdf');
        
        document.getElementById('loading').remove();
        btnImprimir.style.display = 'block';
    }).catch(error => {
        console.error('Error al generar PDF:', error);
        document.getElementById('loading').remove();
        btnImprimir.style.display = 'block';
        alert('Error al generar el PDF. Por favor intente nuevamente.');
    });
}
</script>

<?php $contenido = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
