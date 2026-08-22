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
        
        <div class="main-content">
            <div class="topbar">
                <h1><?= $titulo ?? '' ?></h1>
            </div>
            
            <div class="content-area">
                <?= $contenido ?? '' ?>
            </div>
        </div>
    </div>
</body>
</html>
