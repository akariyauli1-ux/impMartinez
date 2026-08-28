<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Rol - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    
    <div class="card" style="max-width: 900px; width: 90%; margin: 20px auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <div class="card-header" style="text-align: center; padding: 30px 20px;">
            <h2 style="color: #333; margin-bottom: 10px;">Selecciona tu Rol</h2>
            <p style="color: #666; font-size: 16px;">
                Bienvenido <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>. 
                Tienes múltiples roles asignados. Selecciona con cuál deseas trabajar:
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; padding: 30px;">
            <?php foreach ($roles as $rol): ?>
                <a href="<?= APP_URL ?>/public/auth/seleccionar-rol?rol=<?= $rol ?>" 
                   style="text-decoration: none; color: inherit;">
                    <div class="stat-card" style="cursor: pointer; transition: all 0.3s; text-align: center; padding: 30px 20px; border: 2px solid transparent;">
                        <div style="font-size: 48px; margin-bottom: 15px;">
                            <?php
                            $iconos = [
                                'recepcionista' => '📊',
                                'tecnico' => '🔧',
                                'admin_sucursal' => '🏢',
                                'jefe_tecnico' => '👷',
                                'almacenista' => '📦',
                                'gerente' => '👔',
                                'rrhh' => '👥'
                            ];
                            echo $iconos[$rol] ?? '📋';
                            ?>
                        </div>
                        <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px; color: #333;">
                            <?= ucfirst(str_replace('_', ' ', $rol)) ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; padding: 20px; border-top: 1px solid #eee;">
            <a href="<?= APP_URL ?>/public/logout" style="color: #666; text-decoration: none; font-size: 14px;">
                Cerrar Sesión
            </a>
        </div>
    </div>
    
    <style>
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #667eea;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
</body>
</html>
