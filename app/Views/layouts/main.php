<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php require __DIR__ . '/sidebar.php'; ?>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <div class="main-content">
            <div class="topbar">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h1><?= $titulo ?? '' ?></h1>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="notification-container" id="notificationContainer" style="display: none;">
                        <button class="notification-btn" id="notificationBtn">
                            <span style="font-size: 1.3rem;">🔔</span>
                            <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                        </button>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <strong>Notificaciones</strong>
                            </div>
                            <div class="notification-content" id="notificationContent">
                                <p style="padding: 20px; text-align: center; color: var(--gris);">No hay notificaciones</p>
                            </div>
                        </div>
                    </div>
                    <div class="user-welcome">
                        <span class="welcome-name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></span>
                        <span class="welcome-role"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['usuario_rol'] ?? ''))) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="content-area">
                <?= $contenido ?? '' ?>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle sidebar en móvil
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });
        }
        
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
        
        // Cerrar sidebar al hacer click en un enlace (móvil)
        const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
        
        // Sistema de notificaciones
        const notificationContainer = document.getElementById('notificationContainer');
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationContent = document.getElementById('notificationContent');
        
        let lastNotificationCount = 0;
        
        function verificarNotificaciones() {
            fetch('<?= APP_URL ?>/public/notificacion/verificar')
                .then(response => response.json())
                .then(data => {
                    const notificaciones = data.notificaciones || [];
                    const totalNotificaciones = notificaciones.reduce((sum, n) => sum + (n.cantidad || 0), 0);
                    
                    if (totalNotificaciones > 0) {
                        notificationContainer.style.display = 'block';
                        notificationBadge.textContent = totalNotificaciones;
                        notificationBadge.style.display = 'flex';
                        
                        if (totalNotificaciones > lastNotificationCount && lastNotificationCount >= 0) {
                            const nuevaNotificacion = notificaciones.find(n => n.cantidad > 0);
                            if (nuevaNotificacion) {
                                mostrarAlertaVisual(nuevaNotificacion);
                            }
                        }
                        
                        actualizarContenidoNotificacion(notificaciones);
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                    
                    lastNotificationCount = totalNotificaciones;
                })
                .catch(error => console.error('Error al verificar notificaciones:', error));
        }
        
        function mostrarAlertaVisual(notificacion) {
            const alerta = document.createElement('div');
            alerta.className = 'notification-alert';
            alerta.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: linear-gradient(135deg, #D32F2F, #B71C1C);
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(211, 47, 47, 0.4);
                z-index: 10000;
                animation: slideInRight 0.4s ease-out;
                cursor: pointer;
                max-width: 350px;
            `;
            
            const icono = notificacion.icono || '🔔';
            const mensaje = notificacion.mensaje;
            const url = notificacion.url;
            
            alerta.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="font-size: 1.8rem;">${icono}</div>
                    <div>
                        <div style="font-weight: 700; font-size: 1rem; margin-bottom: 4px;">Nueva Notificacion</div>
                        <div style="font-size: 0.9rem; opacity: 0.95;">${mensaje}</div>
                    </div>
                </div>
            `;
            
            alerta.addEventListener('click', function() {
                this.remove();
                window.location.href = url;
            });
            
            document.body.appendChild(alerta);
            
            setTimeout(() => {
                if (alerta.parentNode) {
                    alerta.style.animation = 'slideOutRight 0.4s ease-out';
                    setTimeout(() => alerta.remove(), 400);
                }
            }, 5000);
        }
        
        function actualizarContenidoNotificacion(notificaciones) {
            if (notificaciones.length === 0) {
                notificationContent.innerHTML = '<p style="padding: 20px; text-align: center; color: var(--gris);">No hay notificaciones</p>';
                return;
            }
            
            let html = '';
            notificaciones.forEach(notif => {
                const icono = notif.icono || '🔔';
                const mensaje = notif.mensaje;
                const url = notif.url;
                const cantidad = notif.cantidad;
                
                html += `
                    <a href="${url}" style="display: block; padding: 16px; text-decoration: none; color: var(--negro); border-bottom: 1px solid var(--gris-claro); transition: background 0.2s;" onmouseover="this.style.background='var(--blanco-humo)'" onmouseout="this.style.background='white'">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="font-size: 1.5rem;">${icono}</div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; margin-bottom: 4px;">${mensaje}</div>
                                <div style="font-size: 0.85rem; color: var(--gris);">Click para ver detalles</div>
                            </div>
                            <div style="background: var(--rojo); color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">
                                ${cantidad}
                            </div>
                        </div>
                    </a>
                `;
            });
            
            notificationContent.innerHTML = html;
        }
        
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('active');
            });
        }
        
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });
        
        verificarNotificaciones();
        setInterval(verificarNotificaciones, 30000);
    </script>
    
    <style>
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .notification-container {
            position: relative;
        }
        
        .notification-btn {
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .notification-btn:hover {
            background: var(--blanco-humo);
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--rojo);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .notification-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            min-width: 320px;
            margin-top: 8px;
            overflow: hidden;
            z-index: 1000;
        }
        
        .notification-dropdown.active {
            display: block;
        }
        
        .notification-header {
            padding: 16px;
            border-bottom: 1px solid var(--gris-claro);
            background: var(--blanco-humo);
        }
    </style>
</body>
</html>
