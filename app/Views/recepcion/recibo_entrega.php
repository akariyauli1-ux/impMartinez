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
    margin: 30px 0;
    text-align: center;
}

.firma-container img {
    max-width: 400px;
    border: 1px solid #ddd;
    padding: 10px;
    background: white;
}

.firma-linea {
    margin-top: 10px;
    border-top: 1px solid #333;
    width: 300px;
    margin-left: auto;
    margin-right: auto;
    padding-top: 5px;
    font-size: 12px;
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
    <div class="recibo-header">
        <?php 
        $sucursalModel = new \Sucursal();
        $logo_data = $sucursalModel->obtenerLogoEmpresa();
        if ($logo_data && file_exists(__DIR__ . '/../../../uploads/logos/' . $logo_data)): 
        ?>
            <img src="<?= APP_URL ?>/uploads/logos/<?= $logo_data ?>" alt="Logo Empresa">
        <?php endif; ?>
        
        <h1><?= htmlspecialchars(APP_NAME) ?></h1>
        <p><strong><?= htmlspecialchars($sucursal['nombre']) ?></strong></p>
        <p><?= htmlspecialchars($sucursal['direccion']) ?></p>
        <p>Tel: <?= htmlspecialchars($sucursal['telefono']) ?></p>
    </div>
    
    <!-- Número de Orden -->
    <div class="recibo-numero">
        <strong>N° de Orden:</strong> ORD-<?= str_pad($equipo['id'], 6, '0', STR_PAD_LEFT) ?>
        <span style="float: right;">
            <strong>Fecha de Entrega:</strong> <?= date('d/m/Y H:i', strtotime($equipo['fecha_entrega'])) ?>
        </span>
    </div>
    
    <!-- Datos del Cliente -->
    <div class="seccion">
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
    
    <!-- Datos del Equipo -->
    <div class="seccion">
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
    
    <!-- Costo de Reparación -->
    <div class="costo-total">
        💰 COSTO FINAL DE REPARACIÓN: S/ <?= number_format($equipo['costo_final'], 2) ?>
    </div>
    
    <?php if ($equipo['costo_estimado'] && $equipo['costo_estimado'] != $equipo['costo_final']): ?>
    <div class="seccion" style="background: #fff3e0; border-left-color: #ff9800;">
        <p><strong>Costo Estimado Original:</strong> S/ <?= number_format($equipo['costo_estimado'], 2) ?></p>
        <p><strong>Diferencia:</strong> S/ <?= number_format($equipo['costo_final'] - $equipo['costo_estimado'], 2) ?></p>
    </div>
    <?php endif; ?>
    
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
    
    <!-- Firma de Recepción -->
    <?php if ($equipo['firma_digital']): ?>
    <div class="firma-container">
        <h3 style="text-align: center; margin-bottom: 20px; color: #2196f3;">✍️ Firma de Recepción del Equipo</h3>
        <img src="<?= $equipo['firma_digital'] ?>" alt="Firma de Recepción">
        <div class="firma-linea">
            <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno']) ?><br>
            <small>Firma al momento de recepción</small><br>
            <small>Fecha: <?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></small>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Firma de Entrega -->
    <?php if ($equipo['firma_entrega']): ?>
    <div class="firma-container">
        <h3 style="text-align: center; margin-bottom: 20px; color: #4caf50;">✍️ Firma de Conformidad de Entrega</h3>
        <img src="<?= $equipo['firma_entrega'] ?>" alt="Firma de Entrega">
        <div class="firma-linea">
            <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno']) ?><br>
            <small>Cliente - DNI: <?= htmlspecialchars($cliente['dni'] ?? 'No especificado') ?></small><br>
            <small>Firma al momento de entrega</small><br>
            <small>Fecha: <?= date('d/m/Y H:i', strtotime($equipo['fecha_entrega'])) ?></small>
        </div>
    </div>
    <?php endif; ?>
    
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
    <div class="footer-recibo">
        <p>Este documento es un comprobante de entrega del equipo reparado.</p>
        <p>El cliente firma conforme con la reparación realizada y el costo final indicado.</p>
        <p><strong><?= htmlspecialchars(APP_NAME) ?></strong> - <?= htmlspecialchars($sucursal['nombre']) ?></p>
        <p>Generado el <?= date('d/m/Y H:i:s') ?></p>
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
