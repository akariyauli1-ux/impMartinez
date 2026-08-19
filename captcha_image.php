<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$codigo = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
$_SESSION['captcha_codigo'] = $codigo;

$ancho = 150;
$alto = 50;
$imagen = imagecreatetruecolor($ancho, $alto);

$color_fondo = imagecolorallocate($imagen, 245, 245, 245);
$color_texto = imagecolorallocate($imagen, 211, 47, 47);
$color_linea = imagecolorallocate($imagen, 180, 180, 180);

imagefilledrectangle($imagen, 0, 0, $ancho, $alto, $color_fondo);

for ($i = 0; $i < 5; $i++) {
    imageline($imagen, rand(0, $ancho), rand(0, $alto), rand(0, $ancho), rand(0, $alto), $color_linea);
}

for ($i = 0; $i < 100; $i++) {
    imagesetpixel($imagen, rand(0, $ancho), rand(0, $alto), $color_linea);
}

$fuente = 'C:/Windows/Fonts/arial.ttf';

$x = 15;
for ($i = 0; $i < strlen($codigo); $i++) {
    $angulo = rand(-15, 15);
    $y = rand(30, 40);
    $tamano = rand(18, 22);
    
    if (file_exists($fuente)) {
        imagettftext($imagen, $tamano, $angulo, $x, $y, $color_texto, $fuente, $codigo[$i]);
    } else {
        imagestring($imagen, 5, $x, $y - 15, $codigo[$i], $color_texto);
    }
    $x += 25;
}

header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
imagepng($imagen);
imagedestroy($imagen);
?>
