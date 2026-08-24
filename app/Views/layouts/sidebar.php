<?php
$menus = [
    'recepcionista' => [
        ['url' => APP_URL . '/public/recepcion', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/recepcion/nuevo-equipo', 'icon' => '📝', 'label' => 'Registrar Cliente y Equipo', 'key' => 'nuevo_equipo'],
        ['url' => APP_URL . '/public/recepcion/mis-registros', 'icon' => '📋', 'label' => 'Mis Registros', 'key' => 'mis_registros'],
    ],
    'tecnico' => [
        ['url' => APP_URL . '/public/tecnico', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/tecnico/mis-trabajos', 'icon' => '🔧', 'label' => 'Mis Trabajos', 'key' => 'mis_trabajos'],
    ],
    'admin_sucursal' => [
        ['url' => APP_URL . '/public/admin-sucursal', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/admin-sucursal/pendientes', 'icon' => '📥', 'label' => 'Equipos Pendientes', 'key' => 'pendientes'],
        ['url' => APP_URL . '/public/admin-sucursal/asignar', 'icon' => '🔀', 'label' => 'Asignar a Sucursal', 'key' => 'asignar'],
        ['url' => APP_URL . '/public/admin-sucursal/asistencia', 'icon' => '📅', 'label' => 'Asistencia', 'key' => 'asistencia'],
        ['url' => APP_URL . '/public/admin-sucursal/inspecciones', 'icon' => '👔', 'label' => 'Limpieza/Uniforme', 'key' => 'inspecciones'],
        ['url' => APP_URL . '/public/admin-sucursal/reportes', 'icon' => '📈', 'label' => 'Reportes', 'key' => 'reportes'],
    ],
    'jefe_tecnico' => [
        ['url' => APP_URL . '/public/jefe-tecnico', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/jefe-tecnico/asignar-tecnicos', 'icon' => '👷', 'label' => 'Asignar a Técnicos', 'key' => 'asignar'],
        ['url' => APP_URL . '/public/jefe-tecnico/seguimiento', 'icon' => '📋', 'label' => 'Seguimiento', 'key' => 'seguimiento'],
    ],
    'almacenista' => [
        ['url' => APP_URL . '/public/almacen', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/almacen/inventario', 'icon' => '📦', 'label' => 'Inventario', 'key' => 'inventario'],
        ['url' => APP_URL . '/public/almacen/pedidos', 'icon' => '📝', 'label' => 'Pedidos', 'key' => 'pedidos'],
        ['url' => APP_URL . '/public/almacen/historial', 'icon' => '📋', 'label' => 'Historial', 'key' => 'historial'],
    ],
    'gerente' => [
        ['url' => APP_URL . '/public/gerente', 'icon' => '📊', 'label' => 'Dashboard General', 'key' => 'dashboard'],
        ['url' => APP_URL . '/public/usuarios', 'icon' => '👥', 'label' => 'Gestión Usuarios', 'key' => 'usuarios'],
        ['url' => APP_URL . '/public/gerente/sucursales', 'icon' => '🏢', 'label' => 'Sucursales', 'key' => 'sucursales'],
        ['url' => APP_URL . '/public/gerente/asistencia', 'icon' => '📅', 'label' => 'Reporte Asistencia', 'key' => 'asistencia'],
        ['url' => APP_URL . '/public/gerente/inspecciones', 'icon' => '👔', 'label' => 'Reporte Inspecciones', 'key' => 'inspecciones'],
        ['url' => APP_URL . '/public/gerente/tecnicos', 'icon' => '🔧', 'label' => 'Trabajo Técnicos', 'key' => 'tecnicos'],
        ['url' => APP_URL . '/public/gerente/almacen', 'icon' => '📦', 'label' => 'Estado Almacén', 'key' => 'almacen'],
        ['url' => APP_URL . '/public/gerente/administradores', 'icon' => '👔', 'label' => 'Admin. Sucursales', 'key' => 'administradores'],
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

$items = $menus[$usuario['rol']] ?? [];
$sucursal_nombre = $usuario['sucursal_nombre'] ?? 'Central';
?>

<div class="sidebar">
    <div class="sidebar-header">
        <?php
        $sucursalModel = new Sucursal();
        $logo_empresa = $sucursalModel->obtenerLogoEmpresa();
        
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
            <div class="role"><?= $rol_labels[$usuario['rol']] ?? $usuario['rol'] ?></div>
        </div>
        <a href="<?= APP_URL ?>/public/logout" class="btn btn-outline btn-sm btn-block">Cerrar Sesión</a>
    </div>
</div>