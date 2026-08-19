<?php
require_once __DIR__ . '/../config/database.php';

function generarCaptcha() {
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
    
    for ($i = 0; $i < 50; $i++) {
        imagesetpixel($imagen, rand(0, $ancho), rand(0, $alto), $color_linea);
    }
    
    $fuente = __DIR__ . '/../assets/fonts/arial.ttf';
    if (!file_exists($fuente)) {
        $fuente = 'C:/Windows/Fonts/arial.ttf';
    }
    
    $x = 15;
    for ($i = 0; $i < strlen($codigo); $i++) {
        $angulo = rand(-15, 15);
        $y = rand(30, 40);
        $tamano = rand(18, 22);
        
        if (file_exists($fuente)) {
            imagettftext($imagen, $tamano, $angulo, $x, $y, $color_texto, $fuente, $codigo[$i]);
        } else {
            imagestring($imagen, 5, $x, $y - 10, $codigo[$i], $color_texto);
        }
        $x += 25;
    }
    
    header('Content-Type: image/png');
    imagepng($imagen);
    imagedestroy($imagen);
}

function verificarCaptcha($codigo_ingresado) {
    if (!isset($_SESSION['captcha_codigo'])) {
        return false;
    }
    $valido = strtoupper($codigo_ingresado) === strtoupper($_SESSION['captcha_codigo']);
    unset($_SESSION['captcha_codigo']);
    return $valido;
}
?>
