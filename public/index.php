<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Core/Router.php';

session_start();

$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri, PHP_URL_PATH);
$uri = str_replace('/impMartines/public', '', $uri);
$uri = rtrim($uri, '/') ?: '/';

$method = $_SERVER['REQUEST_METHOD'];

$router = new Router();

$router->add('GET', '/', 'AuthController', 'login');
$router->add('POST', '/login', 'AuthController', 'authenticate');
$router->add('GET', '/logout', 'AuthController', 'logout');
$router->add('GET', '/captcha', 'AuthController', 'captcha');

$router->add('GET', '/recepcion', 'RecepcionController', 'dashboard');
$router->add('GET', '/recepcion/nuevo-cliente', 'RecepcionController', 'nuevoCliente');
$router->add('POST', '/recepcion/guardar-cliente', 'RecepcionController', 'guardarCliente');
$router->add('GET', '/recepcion/nuevo-equipo', 'RecepcionController', 'nuevoEquipo');
$router->add('POST', '/recepcion/guardar-equipo', 'RecepcionController', 'guardarEquipo');
$router->add('GET', '/recepcion/mis-registros', 'RecepcionController', 'misRegistros');

$router->add('GET', '/admin-sucursal', 'AdminSucursalController', 'dashboard');
$router->add('GET', '/admin-sucursal/pendientes', 'AdminSucursalController', 'pendientes');
$router->add('GET', '/admin-sucursal/asignar', 'AdminSucursalController', 'asignar');
$router->add('POST', '/admin-sucursal/guardar-asignacion', 'AdminSucursalController', 'guardarAsignacion');
$router->add('GET', '/admin-sucursal/asistencia', 'AdminSucursalController', 'asistencia');
$router->add('POST', '/admin-sucursal/guardar-asistencia', 'AdminSucursalController', 'guardarAsistencia');
$router->add('GET', '/admin-sucursal/inspecciones', 'AdminSucursalController', 'inspecciones');
$router->add('POST', '/admin-sucursal/guardar-inspecciones', 'AdminSucursalController', 'guardarInspecciones');
$router->add('GET', '/admin-sucursal/reportes', 'AdminSucursalController', 'reportes');

$router->add('GET', '/tecnico', 'TecnicoController', 'dashboard');
$router->add('GET', '/tecnico/mis-trabajos', 'TecnicoController', 'misTrabajos');
$router->add('POST', '/tecnico/actualizar-trabajo', 'TecnicoController', 'actualizarTrabajo');

$router->add('GET', '/jefe-tecnico', 'JefeTecnicoController', 'dashboard');
$router->add('GET', '/jefe-tecnico/asignar-tecnicos', 'JefeTecnicoController', 'asignarTecnicos');
$router->add('POST', '/jefe-tecnico/guardar-asignacion', 'JefeTecnicoController', 'guardarAsignacion');
$router->add('GET', '/jefe-tecnico/seguimiento', 'JefeTecnicoController', 'seguimiento');

$router->add('GET', '/almacen', 'AlmacenController', 'dashboard');
$router->add('GET', '/almacen/inventario', 'AlmacenController', 'inventario');
$router->add('POST', '/almacen/guardar-repuesto', 'AlmacenController', 'guardarRepuesto');
$router->add('GET', '/almacen/movimientos', 'AlmacenController', 'movimientos');
$router->add('POST', '/almacen/guardar-movimiento', 'AlmacenController', 'guardarMovimiento');
$router->add('GET', '/almacen/pedidos', 'AlmacenController', 'pedidos');
$router->add('POST', '/almacen/guardar-pedido', 'AlmacenController', 'guardarPedido');

$router->add('GET', '/gerente', 'GerenteController', 'dashboard');
$router->add('GET', '/gerente/sucursales', 'GerenteController', 'sucursales');
$router->add('POST', '/gerente/guardar-sucursal', 'GerenteController', 'guardarSucursal');
$router->add('POST', '/gerente/subir-logo', 'GerenteController', 'subirLogo');
$router->add('GET', '/gerente/tecnicos', 'GerenteController', 'tecnicos');
$router->add('GET', '/gerente/almacen', 'GerenteController', 'almacen');
$router->add('GET', '/gerente/administradores', 'GerenteController', 'administradores');
$router->add('GET', '/gerente/asistencia', 'GerenteController', 'asistencia');
$router->add('GET', '/gerente/inspecciones', 'GerenteController', 'inspecciones');

$router->add('GET', '/rrhh', 'RRHHController', 'dashboard');
$router->add('GET', '/rrhh/asistencia', 'RRHHController', 'asistencia');
$router->add('GET', '/rrhh/inspecciones', 'RRHHController', 'inspecciones');
$router->add('GET', '/rrhh/productividad', 'RRHHController', 'productividad');

$router->add('GET', '/usuarios', 'UsuariosController', 'index');
$router->add('POST', '/usuarios/guardar', 'UsuariosController', 'guardar');
$router->add('GET', '/usuarios/editar', 'UsuariosController', 'editar');
$router->add('POST', '/usuarios/actualizar', 'UsuariosController', 'actualizar');
$router->add('POST', '/usuarios/toggle-estado', 'UsuariosController', 'toggleEstado');

$router->dispatch($uri, $method);
