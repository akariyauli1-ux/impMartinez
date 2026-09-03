<?php
$menus = [
    'recepcionista' => [
        ['url' => APP_URL . '/public/recepcion', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/recepcion/nuevo-equipo', 'icon' => '📝', 'label' => 'Registrar Cliente y Equipo', 'key' => 'nuevo_equipo'],
        ['url' => APP_URL . '/public/recepcion/mis-registros', 'icon' => '📋', 'label' => 'Mis Registros', 'key' => 'mis_registros'],
        ['url' => APP_URL . '/public/recepcion/equipos-listos', 'icon' => '📦', 'label' => 'Equipos Listos', 'key' => 'equipos_listos'],
        ['url' => APP_URL . '/public/recepcion/historial-entregas', 'icon' => '✅', 'label' => 'Historial Entregas', 'key' => 'historial_entregas'],
        ['url' => APP_URL . '/public/pedidos', 'icon' => '🛒', 'label' => 'Ventas', 'key' => 'pedidos'],
        ['url' => APP_URL . '/public/pedidos/historial', 'icon' => '📜', 'label' => 'Historial Ventas', 'key' => 'historial_pedidos'],
    ],
    'tecnico' => [
        ['url' => APP_URL . '/public/tecnico', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/tecnico/mis-trabajos', 'icon' => '🔧', 'label' => 'Mis Trabajos', 'key' => 'mis_trabajos'],
        ['url' => APP_URL . '/public/pedidos', 'icon' => '🛒', 'label' => 'Ventas', 'key' => 'pedidos'],
        ['url' => APP_URL . '/public/pedidos/historial', 'icon' => '📜', 'label' => 'Historial Ventas', 'key' => 'historial_pedidos'],
    ],
    'admin_sucursal' => [
        ['url' => APP_URL . '/public/admin-sucursal', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/admin-sucursal/pendientes', 'icon' => '📥', 'label' => 'Equipos Pendientes', 'key' => 'pendientes'],
        ['url' => APP_URL . '/public/admin-sucursal/asignar', 'icon' => '🔀', 'label' => 'Asignar a Sucursal', 'key' => 'asignar'],
        ['url' => APP_URL . '/public/admin-sucursal/asistencia', 'icon' => '📅', 'label' => 'Asistencia', 'key' => 'asistencia'],
        ['url' => APP_URL . '/public/admin-sucursal/inspecciones', 'icon' => '👔', 'label' => 'Limpieza/Uniforme', 'key' => 'inspecciones'],
        ['url' => APP_URL . '/public/admin-sucursal/limpieza-local', 'icon' => '🧹', 'label' => 'Limpieza del Local', 'key' => 'limpieza_local'],
        ['url' => APP_URL . '/public/admin-sucursal/reportes', 'icon' => '📈', 'label' => 'Reportes', 'key' => 'reportes'],
        ['url' => APP_URL . '/public/admin-sucursal/entregas', 'icon' => '✅', 'label' => 'Control Entregas', 'key' => 'entregas'],
        ['url' => APP_URL . '/public/pedidos', 'icon' => '🛒', 'label' => 'Ventas', 'key' => 'pedidos'],
        ['url' => APP_URL . '/public/pedidos/historial', 'icon' => '📜', 'label' => 'Historial Ventas', 'key' => 'historial_pedidos'],
    ],
    'jefe_tecnico' => [
        ['url' => APP_URL . '/public/jefe-tecnico', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/jefe-tecnico/asignar-tecnicos', 'icon' => '👷', 'label' => 'Asignar a Técnicos', 'key' => 'asignar'],
        ['url' => APP_URL . '/public/jefe-tecnico/seguimiento', 'icon' => '📋', 'label' => 'Seguimiento', 'key' => 'seguimiento'],
        ['url' => APP_URL . '/public/pedidos', 'icon' => '🛒', 'label' => 'Ventas', 'key' => 'pedidos'],
        ['url' => APP_URL . '/public/pedidos/historial', 'icon' => '📜', 'label' => 'Historial Ventas', 'key' => 'historial_pedidos'],
    ],
    'almacenista' => [
        ['url' => APP_URL . '/public/almacen', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/almacen/inventario', 'icon' => '📦', 'label' => 'Inventario', 'key' => 'inventario'],
        ['url' => APP_URL . '/public/pedidos/almacen', 'icon' => '📝', 'label' => 'Solicitudes', 'key' => 'pedidos'],
        ['url' => APP_URL . '/public/almacen/historial', 'icon' => '📋', 'label' => 'Historial', 'key' => 'historial'],
    ],
    'gerente' => [
        ['url' => APP_URL . '/public/gerente', 'icon' => '📊', 'label' => 'Dashboard General', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/gerente/trazabilidad', 'icon' => '🔍', 'label' => 'Trazabilidad Equipos', 'key' => 'trazabilidad'],
        ['url' => APP_URL . '/public/usuarios', 'icon' => '👥', 'label' => 'Gestión Usuarios', 'key' => 'usuarios'],
        ['url' => APP_URL . '/public/gerente/sucursales', 'icon' => '🏢', 'label' => 'Sucursales', 'key' => 'sucursales'],
        ['url' => APP_URL . '/public/gerente/asistencia', 'icon' => '📅', 'label' => 'Reporte Asistencia', 'key' => 'asistencia'],
        ['url' => APP_URL . '/public/gerente/inspecciones', 'icon' => '👔', 'label' => 'Reporte Inspecciones', 'key' => 'inspecciones'],
        ['url' => APP_URL . '/public/gerente/tecnicos', 'icon' => '🔧', 'label' => 'Trabajo Técnicos', 'key' => 'tecnicos'],
        ['url' => APP_URL . '/public/gerente/almacen', 'icon' => '📦', 'label' => 'Estado Almacén', 'key' => 'almacen'],
        ['url' => APP_URL . '/public/gerente/administradores', 'icon' => '👔', 'label' => 'Admin. Sucursales', 'key' => 'administradores'],
        ['url' => APP_URL . '/public/pedidos', 'icon' => '🛒', 'label' => 'Ventas', 'key' => 'pedidos'],
        ['url' => APP_URL . '/public/pedidos/historial', 'icon' => '📜', 'label' => 'Historial Ventas', 'key' => 'historial_pedidos'],
    ],
    'rrhh' => [
        ['url' => APP_URL . '/public/rrhh', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/usuarios', 'icon' => '👥', 'label' => 'Gestión Usuarios', 'key' => 'usuarios'],
        ['url' => APP_URL . '/public/rrhh/asistencia', 'icon' => '📅', 'label' => 'Asistencia', 'key' => 'asistencia'],
        ['url' => APP_URL . '/public/rrhh/inspecciones', 'icon' => '👔', 'label' => 'Limpieza/Uniforme', 'key' => 'inspecciones'],
        ['url' => APP_URL . '/public/rrhh/productividad', 'icon' => '📈', 'label' => 'Productividad', 'key' => 'productividad'],
    ]
];

$rol_labels = [
    'recepcionista' => 'Recepcionista',
    'tecnico' => 'Técnico',
    'admin_sucursal' => 'Admin. Sucursal',
    'jefe_tecnico' => 'Jefe Técnico',
    'almacenista' => 'Almacenista',
    'gerente' => 'Gerente',
    'rrhh' => 'Recursos Humanos'
];

// Verificar si $usuario está definido
if (!isset($usuario) || !is_array($usuario)) {
    $usuario = [
        'rol' => $_SESSION['usuario_rol'] ?? 'invitado',
        'sucursal_nombre' => $_SESSION['sucursal_nombre'] ?? 'Central',
        'nombre' => $_SESSION['usuario_nombre'] ?? 'Usuario',
        'apellido_paterno' => ''
    ];
}

// Obtener rol activo (el que el usuario seleccionó)
$rol_activo = $_SESSION['rol_activo'] ?? $_SESSION['usuario_rol'] ?? 'invitado';
$roles_usuario = $_SESSION['usuario_roles'] ?? [$rol_activo];

// Obtener menús solo del rol activo
$items = $menus[$rol_activo] ?? [];

$sucursal_nombre = $usuario['sucursal_nombre'] ?? 'Central';
?>

<div class="sidebar">
    <div class="sidebar-header">
        <?php
        $logo_empresa = null;
        if (class_exists('Sucursal')) {
            $sucursalModel = new Sucursal();
            $logo_empresa = $sucursalModel->obtenerLogoEmpresa();
        }
        
        if ($logo_empresa && file_exists(__DIR__ . '/../../../uploads/logos/' . $logo_empresa)):
        ?>
            <img src="<?= APP_URL ?>/uploads/logos/<?= $logo_empresa ?>" alt="Logo" style="max-width: 100%; max-height: 80px; margin-bottom: 10px;">
        <?php else: ?>
            <h2><?= APP_NAME ?></h2>
        <?php endif; ?>
        <p><?= htmlspecialchars($sucursal_nombre) ?></p>
    </div>
    
    <nav class="sidebar-nav">
        <?php foreach ($items as $item): ?>
            <a href="<?= $item['url'] ?>" class="<?= ($pagina_actual ?? '') === $item['key'] ? 'active' : '' ?>">
                <span class="icon"><?= $item['icon'] ?></span>
                <?= $item['label'] ?>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="name"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido_paterno']) ?></div>
            <div class="role" style="margin-bottom: 10px;">
                <?php 
                // Mostrar rol activo
                echo '<strong>' . ($rol_labels[$rol_activo] ?? $rol_activo) . '</strong>';
                
                // Si tiene múltiples roles, mostrar selector
                if (count($roles_usuario) > 1):
                ?>
                    <div style="margin-top: 10px;">
                        <select onchange="window.location.href='<?= APP_URL ?>/public/auth/cambiar-rol?rol=' + this.value" 
                                style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid var(--gris-claro); font-size: 13px; cursor: pointer;">
                            <?php foreach ($roles_usuario as $rol): ?>
                                <option value="<?= $rol ?>" <?= $rol === $rol_activo ? 'selected' : '' ?>>
                                    <?= $rol_labels[$rol] ?? $rol ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <a href="<?= APP_URL ?>/public/logout" class="btn btn-outline btn-sm btn-block">Cerrar Sesión</a>
    </div>
</div>