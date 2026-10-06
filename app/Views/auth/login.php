<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Iniciar Sesión</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/auth.css">
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
                <div style="position: relative;">
                    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required style="padding-right: 45px;">
                    <button type="button" id="toggle-password" onclick="togglePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #666; padding: 5px;" title="Mostrar/Ocultar contraseña">👁</button>
                </div>
            </div>
            
            <div class="form-group">
                <label>Código de Verificación</label>
                <div class="captcha-container">
                    <img id="captcha-img" src="<?= APP_URL ?>/captcha_image.php?t=<?= time() ?>" alt="Captcha" width="150" height="50">
                    <button type="button" class="captcha-refresh" onclick="document.getElementById('captcha-img').src='<?= APP_URL ?>/captcha_image.php?t='+Date.now()" title="Actualizar código">↻</button>
                    <input type="text" name="captcha" placeholder="Código" required maxlength="5" autocomplete="off">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = document.getElementById('toggle-password');
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }
    </script>
</body>
</html>
