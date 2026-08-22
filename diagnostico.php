<?php
// Diagnóstico del sistema
echo "<h2>Diagnóstico ImpMartínez</h2>";

// Verificar PHP
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Verificar extensión MySQL
echo "<p><strong>MySQLi Extension:</strong> " . (extension_loaded('mysqli') ? '✅ Habilitado' : '❌ Deshabilitado') . "</p>";

// Verificar extensión GD
echo "<p><strong>GD Extension:</strong> " . (extension_loaded('gd') ? '✅ Habilitado' : '❌ Deshabilitado') . "</p>";

// Verificar permisos de escritura
$upload_dirs = [
    'uploads/fotos_equipos',
    'uploads/fotos_usuarios',
    'uploads/logos'
];

echo "<h3>Permisos de carpetas:</h3>";
foreach ($upload_dirs as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    if (is_dir($full_path)) {
        $writable = is_writable($full_path);
        echo "<p><strong>$dir:</strong> " . ($writable ? '✅ Escribible' : '❌ No escribible') . "</p>";
    } else {
        echo "<p><strong>$dir:</strong> ❌ No existe</p>";
    }
}

// Verificar conexión a base de datos
echo "<h3>Conexión a Base de Datos:</h3>";
try {
    require_once __DIR__ . '/config/config.php';
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo "<p><strong>Estado:</strong> ❌ Error: " . $conn->connect_error . "</p>";
    } else {
        echo "<p><strong>Estado:</strong> ✅ Conectado</p>";
        echo "<p><strong>Base de datos:</strong> " . DB_NAME . "</p>";
        
        // Verificar tablas
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "<p><strong>Tablas:</strong> " . $result->num_rows . "</p>";
        }
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p><strong>Estado:</strong> ❌ Error: " . $e->getMessage() . "</p>";
}

// Enlaces de prueba
echo "<h3>Enlaces de prueba:</h3>";
echo "<p><a href='/impMartines/public/'>Ir al Login</a></p>";
echo "<p><a href='/impMartines/public/usuarios'>Ir a Usuarios</a></p>";
?>
