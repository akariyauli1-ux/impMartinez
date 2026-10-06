<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Asistencia - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 10vh; display: flex; align-items: center; justify-content: center;">
    
    <div class="card" style="max-width: 500px; width: 90%; margin: 20px auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <div class="card-header" style="text-align: center; padding: 30px 20px;">
            <h2 style="color: #333; margin-bottom: 10px;">Registrar Asistencia</h2>
            <p style="color: #666; font-size: 16px;">
                Bienvenido <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>
            </p>
            <p style="color: #999; font-size: 14px; margin-top: 10px;">
                Fecha: <?= date('d/m/Y') ?>
            </p>
        </div>
        
        <div style="padding: 30px;">
            <form method="POST" action="<?= APP_URL ?>/public/auth/guardar-asistencia">
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="font-size: 72px; margin-bottom: 20px;">⏰</div>
                    <p style="color: #666; font-size: 16px; margin-bottom: 10px;">
                        Presiona el botón para registrar tu hora de entrada
                    </p>
                    <p style="color: #999; font-size: 14px;">
                        Hora actual: <strong id="horaActual"><?= date('H:i:s') ?></strong>
                    </p>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 18px;">
                    ✓ Registrar Entrada
                </button>
            </form>
        </div>
        
        <div style="text-align: center; padding: 20px; border-top: 1px solid #eee;">
            <a href="<?= APP_URL ?>/public/logout" style="color: #666; text-decoration: none; font-size: 14px;">
                Cerrar Sesión
            </a>
        </div>
    </div>
    
    <script>
        // Actualizar hora cada segundo
        function actualizarHora() {
            const ahora = new Date();
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');
            document.getElementById('horaActual').textContent = `${horas}:${minutos}:${segundos}`;
        }
        
        setInterval(actualizarHora, 1000);
    </script>
</body>
</html>
