<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Recibo - <?= htmlspecialchars($orden) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            margin-top: 15px;
            font-weight: bold;
        }
        
        .content {
            padding: 30px;
        }
        
        .section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #4caf50;
            border-radius: 4px;
        }
        
        .section h3 {
            color: #2e7d32;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-item strong {
            color: #555;
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        
        .info-item span {
            color: #333;
            font-size: 16px;
        }
        
        .costo-box {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        
        .costo-box .monto {
            font-size: 32px;
            font-weight: bold;
            color: #2e7d32;
        }
        
        .costo-box .label {
            color: #555;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .footer {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #e0e0e0;
        }
        
        .verificado {
            background: #4caf50;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 0;
            font-weight: bold;
        }
        
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Recibo Verificado</h1>
            <p>Este recibo ha sido emitido por <?= htmlspecialchars($sucursal['nombre']) ?></p>
            <div class="badge">Orden: <?= htmlspecialchars($orden) ?></div>
        </div>
        
        <div class="content">
            <div class="section">
                <h3>👤 Datos del Cliente</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Nombre Completo</strong>
                        <span><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno'] . ' ' . ($cliente['apellido_materno'] ?? '')) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>DNI</strong>
                        <span><?= htmlspecialchars($cliente['dni'] ?? 'No registrado') ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Teléfono</strong>
                        <span><?= htmlspecialchars($cliente['telefono']) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>📱 Datos del Equipo</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Tipo de Equipo</strong>
                        <span><?= htmlspecialchars(ucfirst($equipo['tipo_equipo'])) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Marca</strong>
                        <span><?= htmlspecialchars($equipo['marca']) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Modelo</strong>
                        <span><?= htmlspecialchars($equipo['modelo'] ?? 'No especificado') ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Número de Serie</strong>
                        <span><?= htmlspecialchars($equipo['numero_serie'] ?? 'No especificado') ?></span>
                    </div>
                </div>
                <div class="info-item" style="margin-top: 15px;">
                    <strong>Falla Reportada</strong>
                    <span><?= htmlspecialchars($equipo['descripcion_falla']) ?></span>
                </div>
            </div>
            
            <div class="costo-box">
                <div class="label">Costo Final de Reparación</div>
                <div class="monto">S/ <?= number_format($equipo['costo_final'] ?? 0, 2) ?></div>
                <?php if (!empty($equipo['costo_estimado']) && $equipo['costo_estimado'] != $equipo['costo_final']): ?>
                    <div style="margin-top: 10px; font-size: 14px; color: #666;">
                        Costo estimado original: S/ <?= number_format($equipo['costo_estimado'], 2) ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h3>📋 Información de Entrega</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Fecha de Recepción</strong>
                        <span><?= date('d/m/Y H:i', strtotime($equipo['fecha_registro'])) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Fecha de Entrega</strong>
                        <span><?= !empty($equipo['fecha_entrega']) ? date('d/m/Y H:i', strtotime($equipo['fecha_entrega'])) : 'Pendiente' ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Estado</strong>
                        <span class="verificado"><?= ucfirst($equipo['estado']) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Sucursal</strong>
                        <span><?= htmlspecialchars($sucursal['nombre']) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p><strong><?= htmlspecialchars(APP_NAME) ?></strong> - <?= htmlspecialchars($sucursal['nombre']) ?></p>
            <p>Este documento es una verificación digital del recibo de servicio.</p>
            <p>Verificado el <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
