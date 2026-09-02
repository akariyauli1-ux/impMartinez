<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Models/Equipo.php';
require_once __DIR__ . '/../app/Models/PedidoRepuesto.php';

session_start();

echo "<h2>Debug Notificaciones</h2>";

if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>No hay sesión activa. Inicia sesión primero.</p>";
    echo "<p><a href='" . APP_URL . "/public/'>Ir al login</a></p>";
    exit;
}

echo "<h3>Información de Sesión:</h3>";
echo "<ul>";
echo "<li>Usuario ID: " . $_SESSION['usuario_id'] . "</li>";
echo "<li>Nombre: " . ($_SESSION['usuario_nombre'] ?? 'N/A') . "</li>";
echo "<li>Rol Activo: " . ($_SESSION['rol_activo'] ?? 'N/A') . "</li>";
echo "<li>Sucursal ID: " . ($_SESSION['sucursal_id'] ?? 'N/A') . "</li>";
echo "</ul>";

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol_activo'] ?? $_SESSION['usuario_rol'] ?? '';
$sucursal_id = $_SESSION['sucursal_id'] ?? null;

echo "<h3>Notificaciones para rol '$rol':</h3>";

$equipoModel = new Equipo();
$pedidoModel = new PedidoRepuesto();

// Pedidos
if ($rol === 'almacenista') {
    $cantidad_pedidos = $pedidoModel->contarPendientesAlmacen();
    echo "<p>Pedidos pendientes de respuesta: <strong>$cantidad_pedidos</strong></p>";
} else {
    $cantidad_pedidos = $pedidoModel->contarPendientesConfirmacion($usuario_id);
    echo "<p>Respuestas de almacén pendientes de confirmación: <strong>$cantidad_pedidos</strong></p>";
}

// Trabajos según rol
if ($rol === 'tecnico') {
    $trabajos_nuevos = $equipoModel->contarTrabajosNuevosParaTecnico($usuario_id);
    echo "<p>Trabajos nuevos asignados: <strong>$trabajos_nuevos</strong></p>";
} elseif ($rol === 'jefe_tecnico') {
    echo "<p>Buscando trabajos pendientes para sucursal_id: <strong>$sucursal_id</strong></p>";
    $trabajos_pendientes = $equipoModel->contarTrabajosPendientesAsignarJefe($sucursal_id);
    echo "<p>Trabajos pendientes de asignar a técnico: <strong>$trabajos_pendientes</strong></p>";
} elseif ($rol === 'recepcionista') {
    $trabajos_completados = $equipoModel->contarTrabajosCompletadosParaRecepcion($sucursal_id);
    echo "<p>Trabajos completados listos para entrega: <strong>$trabajos_completados</strong></p>";
} elseif ($rol === 'admin_sucursal') {
    $trabajos_nuevos = $equipoModel->contarTrabajosNuevosParaAdmin($sucursal_id);
    echo "<p>Equipos nuevos pendientes de asignar sucursal: <strong>$trabajos_nuevos</strong></p>";
}

echo "<h3>Detalle de equipos en estado 'asignado_sucursal' sin técnico asignado:</h3>";

$db = Database::getInstance()->getConnection();
$sql = "SELECT e.id, e.tipo_equipo, e.marca, e.modelo, e.estado, e.sucursal_actual_id, e.sucursal_origen_id
        FROM equipos e 
        WHERE e.estado = 'asignado_sucursal' 
        AND e.id NOT IN (SELECT equipo_id FROM asignaciones_tecnico)
        ORDER BY e.sucursal_actual_id, e.id";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Estado</th><th>Sucursal Actual</th><th>Sucursal Origen</th>";
    echo "</tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['tipo_equipo']}</td>";
        echo "<td>{$row['marca']}</td>";
        echo "<td>{$row['modelo']}</td>";
        echo "<td>{$row['estado']}</td>";
        echo "<td>{$row['sucursal_actual_id']}</td>";
        echo "<td>{$row['sucursal_origen_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay equipos en estado 'asignado_sucursal' sin técnico asignado.</p>";
}

echo "<h3>Endpoint JSON:</h3>";
echo "<p><a href='" . APP_URL . "/public/notificacion/verificar' target='_blank'>Ver respuesta JSON del endpoint</a></p>";

echo "<hr>";
echo "<p><a href='" . APP_URL . "/public/'>Volver al inicio</a></p>";
