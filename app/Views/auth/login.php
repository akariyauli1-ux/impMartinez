<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Iniciar Sesión</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <style>
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .captcha-container img {
            border-radius: 8px;
            border: 2px solid #E0E0E0;
            max-width: 100%;
            height: auto;
        }
        .captcha-refresh {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #D32F2F;
            padding: 5px;
        }
        .captcha-container input {
            flex: 1;
            min-width: 100px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
                margin: 10px;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
            
            .login-header p {
                font-size: 0.85rem;
            }
            
            .captcha-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .captcha-container img {
                width: 100%;
                max-width: 200px;
                margin: 0 auto;
            }
            
            .captcha-refresh {
                align-self: center;
            }
        }
    </style>
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <h1><?= APP_NAME ?></h1>
            <p>Sistema de Gestión de Servicio Técnico</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?= APP_URL ?>/public/login" class="login-form">
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" placeholder="Ingrese su apellido" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="carnet">Número de Carnet</label>
                <input type="text" id="carnet" name="carnet" placeholder="Ingrese su número de carnet" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
            </div>
            
            <div class="form-group">
                <label>Código de Verificación</label>
                <div class="captcha-container">
                    <img id="captcha-img" src="<?= APP_URL ?>/public/captcha?t=<?= time() ?>" alt="Captcha" width="150" height="50">
                    <button type="button" class="captcha-refresh" onclick="document.getElementById('captcha-img').src='<?= APP_URL ?>/public/captcha?t='+Date.now()" title="Actualizar código">↻</button>
                    <input type="text" name="captcha" placeholder="Código" required maxlength="5" autocomplete="off">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
