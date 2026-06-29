<?php
use CodeIgniter\Router\RouteCollection;

// ─── Auth ───
$routes->get('/',            'AuthController::index');
$routes->get('login',        'AuthController::index');
$routes->post('login',      'AuthController::login');
$routes->get('logout',      'AuthController::logout');
$routes->post('caja/abrir', 'AuthController::abrirCaja');

// ─── Ventas (POS) ───
$routes->get('ventas',          'VentaController::index');
$routes->get('ventas/buscarC',  'VentaController::buscarPorCodigo');
$routes->get('ventas/buscarN',  'VentaController::buscarPorNombre');
$routes->post('ventas/vender',  'VentaController::vender');
$routes->get('ventas/ticket',   'VentaController::ticket');
$routes->get('ventas/ticket/(:num)', 'VentaController::ticketById/$1');
$routes->post('ventas/anular/(:num)', 'VentaController::anular/$1');
$routes->get('ventas/detalle/(:num)', 'VentaController::detalle/$1');

// ─── Productos ───
$routes->get('productos',             'ProductoController::index');
$routes->get('productos/buscar',      'ProductoController::buscar');
$routes->get('productos/listar',      'ProductoController::listar');
$routes->post('productos/crear',      'ProductoController::crear');
$routes->post('productos/editar/(:num)', 'ProductoController::editar/$1');
$routes->post('productos/eliminar/(:num)', 'ProductoController::eliminar/$1');

// ─── Usuarios ───
$routes->get('usuarios',              'UsuarioController::index');
$routes->get('usuarios/listar',       'UsuarioController::listar');
$routes->post('usuarios/crear',       'UsuarioController::crear');
$routes->post('usuarios/editar/(:num)',   'UsuarioController::editar/$1');
$routes->post('usuarios/eliminar/(:num)', 'UsuarioController::eliminar/$1');

// ─── Reportes ───
$routes->get('reportes',              'ReporteController::index');
$routes->post('reportes/buscar',      'ReporteController::buscar');
$routes->post('reportes/caja/(:segment)', 'ReporteController::editarCaja/$1');

// Devoluciones
$routes->get('devoluciones/migrar', 'DevolucionController::migrar');
$routes->post('devoluciones/procesar', 'DevolucionController::procesar');

// ─── Ajustes (solo superusuario master=3) ───
$routes->get('ajustes',                   'AjustesController::index');
$routes->post('ajustes/negocio',          'AjustesController::guardarNegocio');
$routes->post('ajustes/licencia',         'AjustesController::guardarLicencia');
