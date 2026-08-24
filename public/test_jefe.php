<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Models/Usuario.php';

session_start();

echo "<h2>Test Jefe Técnico</h2>";
echo "<pre>";

echo "Session:\n";
print_r($_SESSION);

echo "\n\nProbando obtenerTecnicosPorSucursal:\n";
try {
    $usuarioModel = new Usuario();
    $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
    $tecnicos = $usuarioModel->obtenerTecnicosPorSucursal($sucursal_id);
    echo "Técnicos encontrados: " . count($tecnicos) . "\n";
    print_r($tecnicos);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
