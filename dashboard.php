<?php
require_once __DIR__ . '/includes/auth.php';
verificarSesion();
header('Location: ' . redirigirSegunRol());
exit;
?>
