<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Entrega - <?= APP_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
        }
        .card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .card-header {
            padding: 24px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .card-header h1 {
            font-size: 1.4rem;
            color: #333;
        }
        .card-header p {
            color: #666;
            font-size: 0.9rem;
            margin-top: 4px;
        }
        .card-body {
            padding: 24px;
        }
        
        .resultado-valido {
            text-align: center;
            padding: 30px;
        }
        .icono-valido {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }
        .icono-valido span {
            font-size: 3rem;
            color: white;
        }
        .titulo-valido {
            font-size: 1.5rem;
            color: #2E7D32;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .subtitulo-valido {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 24px;
        }
        
        .datos-entrega {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            text-align: left;
        }
        .datos-entrega h3 {
            font-size: 0.85rem;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }
        .dato-fila {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        .dato-fila:last-child {
            border-bottom: none;
        }
        .dato-label {
            color: #666;
        }
        .dato-valor {
            font-weight: 600;
            color: #333;
            text-align: right;
        }
        
        .resultado-invalido {
            text-align: center;
            padding: 30px;
        }
        .icono-invalido {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f44336, #C62828);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: shake 0.5s ease-out;
        }
        .icono-invalido span {
            font-size: 3rem;
            color: white;
        }
        .titulo-invalido {
            font-size: 1.5rem;
            color: #C62828;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .subtitulo-invalido {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        .alerta-fraude {
            background: #FFEBEE;
            border: 2px solid #EF9A9A;
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
        }
        .alerta-fraude p {
            color: #C62828;
            font-size: 0.9rem;
        }
        
        .hash-info {
            margin-top: 20px;
            padding: 12px;
            background: #f5f5f5;
            border-radius: 8px;
            font-size: 0.75rem;
            color: #999;
            word-break: break-all;
        }
        .hash-info strong {
            color: #666;
        }
        
        .footer-card {
            padding: 16px 24px;
            background: #f8f9fa;
            text-align: center;
            font-size: 0.8rem;
            color: #999;
            border-top: 1px solid #eee;
        }
        .footer-card img {
            max-height: 30px;
            margin-bottom: 8px;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>🔒 Verificación de Seguridad</h1>
                <p><?= APP_NAME ?> - Sistema de Gestión de Servicio Técnico</p>
            </div>
            
            <div class="card-body">
                <?php if ($resultado): ?>
                    <div class="resultado-valido">
                        <div class="icono-valido">
                            <span>✓</span>
                        </div>
                        <div class="titulo-valido">Documento AUTÉNTICO</div>
                        <div class="subtitulo-valido">Este comprobante de entrega ha sido verificado exitosamente</div>
                        
                        <div class="datos-entrega">
                            <h3>Detalles de la Entrega</h3>
                            <div class="dato-fila">
                                <span class="dato-label">N° Orden:</span>
                                <span class="dato-valor">ORD-<?= str_pad($resultado['id'], 6, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <div class="dato-fila">
                                <span class="dato-label">Cliente:</span>
                                <span class="dato-valor"><?= htmlspecialchars($resultado['cliente_nombre'] . ' ' . $resultado['cliente_ap']) ?></span>
                            </div>
                            <?php if (!empty($resultado['cliente_dni'])): ?>
                            <div class="dato-fila">
                                <span class="dato-label">DNI:</span>
                                <span class="dato-valor"><?= htmlspecialchars($resultado['cliente_dni']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="dato-fila">
                                <span class="dato-label">Equipo:</span>
                                <span class="dato-valor"><?= htmlspecialchars(ucfirst($resultado['tipo_equipo']) . ' ' . $resultado['marca'] . ' ' . $resultado['modelo']) ?></span>
                            </div>
                            <div class="dato-fila">
                                <span class="dato-label">Fecha Entrega:</span>
                                <span class="dato-valor"><?= date('d/m/Y H:i', strtotime($resultado['fecha_entrega'])) ?></span>
                            </div>
                            <div class="dato-fila">
                                <span class="dato-label">Costo Final:</span>
                                <span class="dato-valor" style="color: #2E7D32; font-size: 1.1rem;">S/ <?= number_format($resultado['costo_final'] ?? 0, 2) ?></span>
                            </div>
                            <div class="dato-fila">
                                <span class="dato-label">Sucursal:</span>
                                <span class="dato-valor"><?= htmlspecialchars($resultado['sucursal_nombre'] ?? 'N/A') ?></span>
                            </div>
                        </div>
                        
                        <div class="hash-info">
                            <strong>Código de seguridad:</strong><br>
                            <?= htmlspecialchars(substr($resultado['hash_seguridad'], 0, 32)) ?>...
                        </div>
                    </div>
                <?php else: ?>
                    <div class="resultado-invalido">
                        <div class="icono-invalido">
                            <span>✗</span>
                        </div>
                        <div class="titulo-invalido">Documento NO VÁLIDO</div>
                        <div class="subtitulo-invalido">
                            <?php if (!$hash): ?>
                                No se proporcionó un código de verificación válido.
                            <?php else: ?>
                                El código de seguridad no coincide con ningún registro en el sistema.
                            <?php endif; ?>
                        </div>
                        
                        <div class="alerta-fraude">
                            <p>
                                <strong>⚠️ Posible documento fraudulento.</strong><br>
                                Si sospecha que este comprobante ha sido alterado o falsificado, 
                                comuníquese inmediatamente con <?= APP_NAME ?> para verificar la autenticidad del documento.
                            </p>
                        </div>
                        
                        <?php if ($hash): ?>
                        <div class="hash-info">
                            <strong>Código ingresado:</strong><br>
                            <?= htmlspecialchars(substr($hash, 0, 32)) ?>...
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="footer-card">
                <p><strong><?= APP_NAME ?></strong></p>
                <p>Verificación de autenticidad de comprobantes de entrega</p>
                <p style="margin-top: 8px;"><?= date('d/m/Y H:i:s') ?></p>
            </div>
        </div>
    </div>
</body>
</html>