<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
$usuario = verificarRol(['recepcionista']);

$conn = getConexion();
$clientes = $conn->query("SELECT id, CONCAT(nombre, ' ', apellido_paterno, ' ', IFNULL(apellido_materno,'')) as nombre_completo, telefono FROM clientes ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $tipo_equipo = sanitizar($_POST['tipo_equipo']);
    $marca = sanitizar($_POST['marca']);
    $modelo = sanitizar($_POST['modelo']);
    $numero_serie = sanitizar($_POST['numero_serie']);
    $accesorios = sanitizar($_POST['accesorios']);
    $descripcion_falla = sanitizar($_POST['descripcion_falla']);
    
    $fotos = [];
    if (!empty($_FILES['fotos']['name'][0])) {
        $upload_dir = __DIR__ . '/../../uploads/fotos_equipos/';
        for ($i = 0; $i < count($_FILES['fotos']['name']); $i++) {
            if ($_FILES['fotos']['error'][$i] === 0) {
                $ext = pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION);
                $nombre_archivo = uniqid('foto_') . '.' . $ext;
                if (move_uploaded_file($_FILES['fotos']['tmp_name'][$i], $upload_dir . $nombre_archivo)) {
                    $fotos[] = $nombre_archivo;
                }
            }
        }
    }
    
    $fotos_json = json_encode($fotos);
    $sucursal_id = $usuario['sucursal_id'];
    
    $stmt = $conn->prepare("INSERT INTO equipos (cliente_id, tipo_equipo, marca, modelo, numero_serie, accesorios, descripcion_falla, fotos, estado, recepcionista_id, sucursal_origen_id, sucursal_actual_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente_asignacion', ?, ?, ?)");
    $stmt->bind_param("isssssssiii", $cliente_id, $tipo_equipo, $marca, $modelo, $numero_serie, $accesorios, $descripcion_falla, $fotos_json, $usuario['id'], $sucursal_id, $sucursal_id);
    
    if ($stmt->execute()) {
        $mensaje = 'Equipo registrado correctamente. Queda pendiente de asignación.';
    } else {
        $mensaje = 'Error al registrar el equipo: ' . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Equipo - ImpMartínez</title>
    <link rel="stylesheet" href="/impMartines/assets/css/style.css">
</head>
<body>
    <div class="app-layout">
        <?php renderSidebar($usuario, 'nuevo_equipo'); ?>
        
        <div class="main-content">
            <?php renderTopbar('Registrar Nuevo Equipo'); ?>
            
            <div class="content-area">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Datos del Equipo</h2>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="cliente_id">Cliente *</label>
                            <select id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccione un cliente</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= sanitizar($c['nombre_completo']) ?> - <?= sanitizar($c['telefono']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_equipo">Tipo de Equipo *</label>
                                <select id="tipo_equipo" name="tipo_equipo" required>
                                    <option value="">Seleccione</option>
                                    <option value="celular">Celular</option>
                                    <option value="laptop">Laptop</option>
                                    <option value="pc">PC</option>
                                    <option value="tv">TV</option>
                                    <option value="radio">Radio</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="marca">Marca</label>
                                <input type="text" id="marca" name="marca">
                            </div>
                            <div class="form-group">
                                <label for="modelo">Modelo</label>
                                <input type="text" id="modelo" name="modelo">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="numero_serie">Número de Serie</label>
                                <input type="text" id="numero_serie" name="numero_serie">
                            </div>
                            <div class="form-group">
                                <label for="accesorios">Accesorios</label>
                                <input type="text" id="accesorios" name="accesorios" placeholder="Cargador, funda, etc.">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion_falla">Descripción de la Falla *</label>
                            <textarea id="descripcion_falla" name="descripcion_falla" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="fotos">Fotos del Equipo (máx. 5)</label>
                            <input type="file" id="fotos" name="fotos[]" accept="image/*" multiple>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Registrar Equipo</button>
                        <a href="/impMartines/modules/recepcion/dashboard.php" class="btn btn-outline">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
