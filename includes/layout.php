<?php
function renderSidebar($usuario, $pagina_actual = '') {
    $menus = [
        'recepcionista' => [
            ['url' => '/impMartines/modules/recepcion/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
            ['url' => '/impMartines/modules/recepcion/nuevo_cliente.php', 'icon' => '👤', 'label' => 'Nuevo Cliente', 'key' => 'nuevo_cliente'],
            ['url' => '/impMartines/modules/recepcion/nuevo_equipo.php', 'icon' => '📱', 'label' => 'Registrar Equipo', 'key' => 'nuevo_equipo'],
            ['url' => '/impMartines/modules/recepcion/mis_registros.php', 'icon' => '📋', 'label' => 'Mis Registros', 'key' => 'mis_registros'],
        ],
        'tecnico' => [
            ['url' => '/impMartines/modules/tecnico/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
            ['url' => '/impMartines/modules/tecnico/mis_trabajos.php', 'icon' => '🔧', 'label' => 'Mis Trabajos', 'key' => 'mis_trabajos'],
        ],
        'admin_sucursal' => [
            ['url' => '/impMartines/modules/admin_sucursal/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
            ['url' => '/impMartines/modules/admin_sucursal/pendientes.php', 'icon' => '📥', 'label' => 'Equipos Pendientes', 'key' => 'pendientes'],
            ['url' => '/impMartines/modules/admin_sucursal/asignar.php', 'icon' => '🔀', 'label' => 'Asignar a Sucursal', 'key' => 'asignar'],
            ['url' => '/impMartines/modules/admin_sucursal/reportes.php', 'icon' => '📈', 'label' => 'Reportes', 'key' => 'reportes'],
        ],
        'jefe_tecnico' => [
            ['url' => '/impMartines/modules/jefe_tecnico/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
            ['url' => '/impMartines/modules/jefe_tecnico/asignar_tecnicos.php', 'icon' => '👷', 'label' => 'Asignar a Técnicos', 'key' => 'asignar'],
            ['url' => '/impMartines/modules/jefe_tecnico/seguimiento.php', 'icon' => '📋', 'label' => 'Seguimiento', 'key' => 'seguimiento'],
        ],
        'almacenista' => [
            ['url' => '/impMartines/modules/almacen/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
            ['url' => '/impMartines/modules/almacen/inventario.php', 'icon' => '📦', 'label' => 'Inventario', 'key' => 'inventario'],
            ['url' => '/impMartines/modules/almacen/movimientos.php', 'icon' => '🔄', 'label' => 'Movimientos', 'key' => 'movimientos'],
            ['url' => '/impMartines/modules/almacen/pedidos.php', 'icon' => '📝', 'label' => 'Pedidos', 'key' => 'pedidos'],
        ],
        'gerente' => [
            ['url' => '/impMartines/modules/gerente/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard General', 'key' => 'dashboard'],
            ['url' => '/impMartines/modules/usuarios/index.php', 'icon' => '👥', 'label' => 'Gestión Usuarios', 'key' => 'usuarios'],
            ['url' => '/impMartines/modules/gerente/sucursales.php', 'icon' => '🏢', 'label' => 'Sucursales', 'key' => 'sucursales'],
            ['url' => '/impMartines/modules/gerente/tecnicos.php', 'icon' => '🔧', 'label' => 'Trabajo Técnicos', 'key' => 'tecnicos'],
            ['url' => '/impMartines/modules/gerente/almacen.php', 'icon' => '📦', 'label' => 'Estado Almacén', 'key' => 'almacen'],
            ['url' => '/impMartines/modules/gerente/administradores.php', 'icon' => '👔', 'label' => 'Admin. Sucursales', 'key' => 'administradores'],
        ],
        'rrhh' => [
            ['url' => '/impMartines/modules/rrhh/dashboard.php', 'icon' => '📊', 'label' => 'Dashboard', 'key' => 'dashboard'],
            ['url' => '/impMartines/modules/usuarios/index.php', 'icon' => '👥', 'label' => 'Gestión Usuarios', 'key' => 'usuarios'],
            ['url' => '/impMartines/modules/rrhh/asistencia.php', 'icon' => '📅', 'label' => 'Asistencia', 'key' => 'asistencia'],
            ['url' => '/impMartines/modules/rrhh/inspecciones.php', 'icon' => '👔', 'label' => 'Limpieza/Uniforme', 'key' => 'inspecciones'],
            ['url' => '/impMartines/modules/rrhh/productividad.php', 'icon' => '📈', 'label' => 'Productividad', 'key' => 'productividad'],
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
            <h2>ImpMartínez</h2>
            <p><?= sanitizar($sucursal_nombre) ?></p>
        </div>
        
        <nav class="sidebar-nav">
            <?php foreach ($items as $item): ?>
                <a href="<?= $item['url'] ?>" class="<?= $pagina_actual === $item['key'] ? 'active' : '' ?>">
                    <span class="icon"><?= $item['icon'] ?></span>
                    <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="name"><?= sanitizar($usuario['nombre'] . ' ' . $usuario['apellido_paterno']) ?></div>
                <div class="role"><?= $rol_labels[$usuario['rol']] ?? $usuario['rol'] ?></div>
            </div>
            <a href="/impMartines/logout.php" class="btn btn-outline btn-sm btn-block">Cerrar Sesión</a>
        </div>
    </div>
    <?php
}

function renderTopbar($titulo) {
    ?>
    <div class="topbar">
        <h1><?= sanitizar($titulo) ?></h1>
    </div>
    <?php
}
?>
