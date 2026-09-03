<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$router->add('GET', '/auth/seleccionar-rol', 'AuthController', 'seleccionarRol');
$router->add('GET', '/auth/cambiar-rol', 'AuthController', 'cambiarRol');

$router->add('GET', '/recepcion', 'RecepcionController', 'dashboard');
$router->add('GET', '/recepcion/nuevo-cliente', 'RecepcionController', 'nuevoCliente');
$router->add('POST', '/recepcion/guardar-cliente', 'RecepcionController', 'guardarCliente');
$router->add('GET', '/recepcion/nuevo-equipo', 'RecepcionController', 'nuevoEquipo');
$router->add('POST', '/recepcion/guardar-equipo', 'RecepcionController', 'guardarEquipo');
$router->add('GET', '/recepcion/mis-registros', 'RecepcionController', 'misRegistros');
$router->add('GET', '/recepcion/ver-recibo', 'RecepcionController', 'verRecibo');
$router->add('GET', '/recepcion/equipos-listos', 'RecepcionController', 'equiposListos');
$router->add('POST', '/recepcion/marcar-entregado', 'RecepcionController', 'marcarEntregado');
$router->add('GET', '/recepcion/historial-entregas', 'RecepcionController', 'historialEntregas');
$router->add('GET', '/recepcion/formulario-entrega', 'RecepcionController', 'formularioEntrega');
$router->add('POST', '/recepcion/procesar-entrega', 'RecepcionController', 'procesarEntrega');
$router->add('GET', '/recepcion/ver-recibo-entrega', 'RecepcionController', 'verReciboEntrega');
$router->add('GET', '/recepcion/verificar-qr', 'RecepcionController', 'verificarQR');
$router->add('GET', '/recepcion/verificar-entrega', 'RecepcionController', 'verificarEntrega');

$router->add('GET', '/consulta-almacen/obtener-repuestos', 'ConsultaAlmacenController', 'obtenerRepuestos');

$router->add('GET', '/admin-sucursal', 'AdminSucursalController', 'dashboard');
$router->add('GET', '/admin-sucursal/pendientes', 'AdminSucursalController', 'pendientes');
$router->add('GET', '/admin-sucursal/asignar', 'AdminSucursalController', 'redirigirAsignar');
$router->add('POST', '/admin-sucursal/guardar-asignacion', 'AdminSucursalController', 'guardarAsignacion');
$router->add('GET', '/admin-sucursal/asistencia', 'AdminSucursalController', 'asistencia');
$router->add('POST', '/admin-sucursal/guardar-asistencia', 'AdminSucursalController', 'guardarAsistencia');
$router->add('GET', '/admin-sucursal/inspecciones', 'AdminSucursalController', 'inspecciones');
$router->add('POST', '/admin-sucursal/guardar-inspecciones', 'AdminSucursalController', 'guardarInspecciones');
$router->add('GET', '/admin-sucursal/reportes', 'AdminSucursalController', 'reportes');
$router->add('GET', '/admin-sucursal/entregas', 'AdminSucursalController', 'entregas');
$router->add('GET', '/admin-sucursal/limpieza-local', 'AdminSucursalController', 'limpiezaLocal');
$router->add('POST', '/admin-sucursal/guardar-limpieza-local', 'AdminSucursalController', 'guardarLimpiezaLocal');

$router->add('GET', '/tecnico', 'TecnicoController', 'dashboard');
$router->add('GET', '/tecnico/mis-trabajos', 'TecnicoController', 'misTrabajos');
$router->add('POST', '/tecnico/actualizar-trabajo', 'TecnicoController', 'actualizarTrabajo');
$router->add('POST', '/tecnico/confirmar-recibido', 'TecnicoController', 'confirmarRecibido');
$router->add('POST', '/tecnico/rechazar-trabajo', 'TecnicoController', 'rechazarTrabajo');
$router->add('POST', '/tecnico/solicitar-componente', 'TecnicoController', 'solicitarComponente');
$router->add('GET', '/tecnico/obtener-repuestos', 'TecnicoController', 'obtenerRepuestos');
$router->add('GET', '/tecnico/obtener-costo-equipo', 'TecnicoController', 'obtenerCostoEquipo');

$router->add('GET', '/jefe-tecnico', 'JefeTecnicoController', 'dashboard');
$router->add('GET', '/jefe-tecnico/asignar-tecnicos', 'JefeTecnicoController', 'asignarTecnicos');
$router->add('POST', '/jefe-tecnico/guardar-asignacion', 'JefeTecnicoController', 'guardarAsignacion');
$router->add('GET', '/jefe-tecnico/seguimiento', 'JefeTecnicoController', 'seguimiento');
$router->add('GET', '/jefe-tecnico/obtener-detalles-equipo', 'JefeTecnicoController', 'obtenerDetallesEquipo');
$router->add('POST', '/jefe-tecnico/aprobar-trabajo', 'JefeTecnicoController', 'aprobarTrabajo');

$router->add('GET', '/almacen', 'AlmacenController', 'dashboard');
$router->add('GET', '/almacen/inventario', 'AlmacenController', 'inventario');
$router->add('POST', '/almacen/guardar-repuesto', 'AlmacenController', 'guardarRepuesto');
$router->add('POST', '/almacen/actualizar-repuesto', 'AlmacenController', 'actualizarRepuesto');
$router->add('POST', '/almacen/toggle-descontinuado', 'AlmacenController', 'toggleDescontinuado');
$router->add('POST', '/almacen/editar-categoria', 'AlmacenController', 'editarCategoria');
$router->add('POST', '/almacen/eliminar-categoria', 'AlmacenController', 'eliminarCategoria');
$router->add('GET', '/almacen/pedidos', 'AlmacenController', 'pedidos');
$router->add('POST', '/almacen/guardar-pedido', 'AlmacenController', 'guardarPedido');
$router->add('POST', '/almacen/entregar-solicitud', 'AlmacenController', 'entregarSolicitud');
$router->add('GET', '/almacen/historial', 'AlmacenController', 'historial');

$router->add('GET', '/pedidos', 'PedidoController', 'index');
$router->add('GET', '/pedidos/nuevo', 'PedidoController', 'nuevo');
$router->add('POST', '/pedidos/guardar', 'PedidoController', 'guardar');
$router->add('GET', '/pedidos/historial', 'PedidoController', 'historial');
$router->add('GET', '/pedidos/almacen', 'PedidoController', 'almacen');
$router->add('POST', '/pedidos/responder', 'PedidoController', 'responder');
$router->add('POST', '/pedidos/confirmar-recibido', 'PedidoController', 'confirmarRecibido');
$router->add('POST', '/pedidos/confirmar-leido', 'PedidoController', 'confirmarLeido');
$router->add('POST', '/pedidos/entregar-solicitud', 'PedidoController', 'entregarSolicitud');
$router->add('POST', '/pedidos/marcar-agotado', 'PedidoController', 'marcarAgotado');
$router->add('POST', '/pedidos/comprar-externo', 'PedidoController', 'comprarExterno');
$router->add('POST', '/pedidos/recibir-compra-externa', 'PedidoController', 'recibirCompraExterna');
$router->add('POST', '/pedidos/confirmar-recibido-solicitud', 'PedidoController', 'confirmarRecibidoSolicitud');
$router->add('GET', '/pedidos/notificacion', 'PedidoController', 'notificacion');

$router->add('GET', '/gerente', 'GerenteController', 'dashboard');
$router->add('GET', '/gerente/sucursales', 'GerenteController', 'sucursales');
$router->add('POST', '/gerente/guardar-sucursal', 'GerenteController', 'guardarSucursal');
$router->add('POST', '/gerente/subir-logo', 'GerenteController', 'subirLogo');
$router->add('GET', '/gerente/tecnicos', 'GerenteController', 'tecnicos');
$router->add('GET', '/gerente/almacen', 'GerenteController', 'almacen');
$router->add('GET', '/gerente/administradores', 'GerenteController', 'administradores');
$router->add('GET', '/gerente/asistencia', 'GerenteController', 'asistencia');
$router->add('GET', '/gerente/inspecciones', 'GerenteController', 'inspecciones');
$router->add('GET', '/gerente/trazabilidad', 'GerenteController', 'trazabilidad');
$router->add('GET', '/gerente/trazabilidad-detalle', 'GerenteController', 'trazabilidadDetalle');

$router->add('GET', '/rrhh', 'RRHHController', 'dashboard');
$router->add('GET', '/rrhh/asistencia', 'RRHHController', 'asistencia');
$router->add('GET', '/rrhh/inspecciones', 'RRHHController', 'inspecciones');
$router->add('GET', '/rrhh/productividad', 'RRHHController', 'productividad');

$router->add('GET', '/notificacion/verificar', 'NotificacionController', 'verificar');

$router->add('GET', '/imagen/logo', 'ImagenController', 'logo');
$router->add('GET', '/imagen/foto-usuario', 'ImagenController', 'fotoUsuario');
$router->add('GET', '/imagen/foto-equipo', 'ImagenController', 'fotoEquipo');
$router->add('GET', '/imagen/fotos-equipo', 'ImagenController', 'fotosEquipo');

$router->add('GET', '/usuarios', 'UsuariosController', 'index');
$router->add('POST', '/usuarios/guardar', 'UsuariosController', 'guardar');
$router->add('GET', '/usuarios/editar', 'UsuariosController', 'editar');
$router->add('POST', '/usuarios/actualizar', 'UsuariosController', 'actualizar');
$router->add('POST', '/usuarios/toggle-estado', 'UsuariosController', 'toggleEstado');
$router->add('POST', '/usuarios/gestionar-roles', 'UsuariosController', 'gestionarRoles');
$router->add('GET', '/usuarios/obtener-roles', 'UsuariosController', 'obtenerRoles');

$router->dispatch($uri, $method);
