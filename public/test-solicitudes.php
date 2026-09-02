<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Models/Repuesto.php';
require_once __DIR__ . '/../app/Models/SolicitudComponente.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Debug Solicitudes de Componentes</h2>";

if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: orange;'>No hay sesión activa.</p>";
    echo "<p><a href='" . APP_URL . "/public/' style='background: #D32F2F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Iniciar Sesión</a></p>";
    echo "<hr>";
    
    echo "<h3>Solicitudes de Componentes en la base de datos (sin sesión):</h3>";
    $solicitudModel = new SolicitudComponente();
    $todas_solicitudes = $solicitudModel->obtenerTodas();
    
    if (empty($todas_solicitudes)) {
        echo "<p>No hay solicitudes registradas.</p>";
    } else {
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Técnico</th><th>Equipo</th><th>Repuesto</th><th>Cantidad</th><th>Total</th><th>Estado</th><th>Fecha</th>";
        echo "</tr>";
        foreach ($todas_solicitudes as $s) {
            echo "<tr>";
            echo "<td>{$s['id']}</td>";
            echo "<td>{$s['tecnico_nombre']} {$s['tecnico_ap']}</td>";
            echo "<td>{$s['tipo_equipo']} {$s['equipo_marca']} {$s['equipo_modelo']}</td>";
            echo "<td>{$s['repuesto_nombre']}</td>";
            echo "<td>{$s['cantidad']}</td>";
            echo "<td>S/ " . number_format($s['total'], 2) . "</td>";
            echo "<td>{$s['estado']}</td>";
            echo "<td>{$s['fecha_solicitud']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>Repuestos en el sistema:</h3>";
    $repuestoModel = new Repuesto();
    $repuestos = $repuestoModel->obtenerTodos();
    
    if (empty($repuestos)) {
        echo "<p style='color: red;'>No hay repuestos en el sistema.</p>";
    } else {
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Código</th><th>Nombre</th><th>Marca</th><th>Stock</th><th>Precio</th><th>Sucursal</th>";
        echo "</tr>";
        foreach ($repuestos as $r) {
            echo "<tr>";
            echo "<td>{$r['id']}</td>";
            echo "<td>{$r['codigo']}</td>";
            echo "<td>{$r['nombre']}</td>";
            echo "<td>{$r['marca']}</td>";
            echo "<td>{$r['stock']}</td>";
            echo "<td>S/ " . number_format($r['precio_unitario'], 2) . "</td>";
            echo "<td>" . ($r['sucursal_nombre'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    exit;
}

echo "<h3>Información de Sesión:</h3>";
echo "<ul>";
echo "<li>Usuario ID: " . $_SESSION['usuario_id'] . "</li>";
echo "<li>Nombre: " . ($_SESSION['usuario_nombre'] ?? 'N/A') . "</li>";
echo "<li>Rol Activo: " . ($_SESSION['rol_activo'] ?? 'N/A') . "</li>";
echo "<li>Sucursal ID: " . ($_SESSION['sucursal_id'] ?? 'N/A') . "</li>";
echo "</ul>";

$sucursal_id = $_SESSION['sucursal_id'] ?? 1;

echo "<h3>Repuestos en sucursal $sucursal_id:</h3>";
$repuestoModel = new Repuesto();
$repuestos = $repuestoModel->obtenerPorSucursal($sucursal_id);

if (empty($repuestos)) {
    echo "<p style='color: orange;'>No hay repuestos en esta sucursal. Verificando todas las sucursales...</p>";
    $repuestos = $repuestoModel->obtenerTodos();
}

if (empty($repuestos)) {
    echo "<p style='color: red;'>No hay repuestos en el sistema.</p>";
} else {
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Código</th><th>Nombre</th><th>Marca</th><th>Stock</th><th>Precio</th><th>Sucursal</th>";
    echo "</tr>";
    foreach ($repuestos as $r) {
        echo "<tr>";
        echo "<td>{$r['id']}</td>";
        echo "<td>{$r['codigo']}</td>";
        echo "<td>{$r['nombre']}</td>";
        echo "<td>{$r['marca']}</td>";
        echo "<td>{$r['stock']}</td>";
        echo "<td>S/ " . number_format($r['precio_unitario'], 2) . "</td>";
        echo "<td>" . ($r['sucursal_nombre'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>Solicitudes de Componentes en la base de datos:</h3>";
$solicitudModel = new SolicitudComponente();
$todas_solicitudes = $solicitudModel->obtenerTodas();

if (empty($todas_solicitudes)) {
    echo "<p>No hay solicitudes registradas.</p>";
} else {
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Técnico</th><th>Equipo</th><th>Repuesto</th><th>Cantidad</th><th>Total</th><th>Estado</th><th>Fecha</th>";
    echo "</tr>";
    foreach ($todas_solicitudes as $s) {
        echo "<tr>";
        echo "<td>{$s['id']}</td>";
        echo "<td>{$s['tecnico_nombre']} {$s['tecnico_ap']}</td>";
        echo "<td>{$s['tipo_equipo']} {$s['equipo_marca']} {$s['equipo_modelo']}</td>";
        echo "<td>{$s['repuesto_nombre']}</td>";
        echo "<td>{$s['cantidad']}</td>";
        echo "<td>S/ " . number_format($s['total'], 2) . "</td>";
        echo "<td>{$s['estado']}</td>";
        echo "<td>{$s['fecha_solicitud']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>Endpoint JSON:</h3>";
echo "<p><a href='" . APP_URL . "/public/tecnico/obtener-repuestos' target='_blank'>Ver repuestos (JSON)</a></p>";

echo "<hr>";
echo "<p><a href='" . APP_URL . "/public/'>Volver al inicio</a></p>";
