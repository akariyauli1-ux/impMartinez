<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Models/Equipo.php';

session_start();

echo "<h2>Debug Notificaciones</h2>";
echo "<pre>";
echo "Sesion activa: " . (isset($_SESSION['usuario_id']) ? 'SI' : 'NO') . "\n";
echo "Usuario ID: " . ($_SESSION['usuario_id'] ?? 'N/A') . "\n";
echo "Rol activo: " . ($_SESSION['rol_activo'] ?? 'N/A') . "\n";
echo "Sucursal ID: " . ($_SESSION['sucursal_id'] ?? 'N/A') . "\n";
echo "</pre>";

if (isset($_SESSION['usuario_id'])) {
    $equipoModel = new Equipo();
    $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
    
    echo "<h3>Consultas para sucursal_id = $sucursal_id</h3>";
    echo "<pre>";
    
    $pendientes_jefe = $equipoModel->contarTrabajosPendientesAsignarJefe($sucursal_id);
    echo "Trabajos pendientes para Jefe Tecnico: $pendientes_jefe\n";
    
    $usuario_id = $_SESSION['usuario_id'];
    $trabajos_tecnico = $equipoModel->contarTrabajosNuevosParaTecnico($usuario_id);
    echo "Trabajos nuevos para Tecnico (ID $usuario_id): $trabajos_tecnico\n";
    
    $completados_recep = $equipoModel->contarTrabajosCompletadosParaRecepcion($sucursal_id);
    echo "Trabajos completados para Recepcion: $completados_recep\n";
    
    $nuevos_admin = $equipoModel->contarTrabajosNuevosParaAdmin($sucursal_id);
    echo "Equipos nuevos para Admin: $nuevos_admin\n";
    
    echo "</pre>";
    
    echo "<h3>Equipos en estado asignado_sucursal sin tecnico:</h3>";
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT e.id, e.tipo_equipo, e.marca, e.modelo, e.estado, e.sucursal_actual_id 
            FROM equipos e 
            WHERE e.sucursal_actual_id = ? AND e.estado = 'asignado_sucursal' 
            AND e.id NOT IN (SELECT equipo_id FROM asignaciones_tecnico)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Estado</th><th>Sucursal</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['tipo_equipo']}</td>";
        echo "<td>{$row['marca']}</td>";
        echo "<td>{$row['modelo']}</td>";
        echo "<td>{$row['estado']}</td>";
        echo "<td>{$row['sucursal_actual_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<p><a href='" . APP_URL . "/public/'>Volver al inicio</a></p>";
