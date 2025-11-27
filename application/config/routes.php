<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Rutas de autenticación
$route['login'] = 'login';
$route['login/authenticate'] = 'login/authenticate';
$route['logout'] = 'login/logout';

// Rutas de administrador (ambas versiones para compatibilidad)
$route['admin'] = 'admin/dashboard';
$route['admin/dashboard'] = 'admin/dashboard';
$route['admin/productos'] = 'admin/productos';
$route['admin/crear_producto'] = 'admin/crear_producto';
$route['admin/actualizar_producto'] = 'admin/actualizar_producto';
$route['admin/eliminar_producto/(:num)'] = 'admin/eliminar_producto/$1';
$route['admin/ver_producto/(:num)'] = 'admin/ver_producto/$1';
$route['admin/editar_producto/(:num)'] = 'admin/editar_producto/$1';
$route['admin/obtener_siguiente_codigo'] = 'admin/obtener_siguiente_codigo'; // Nueva ruta
$route['admin/registrar_movimiento'] = 'admin/registrar_movimiento'; // Nueva ruta

// Rutas de referencias
$route['admin/referencias'] = 'admin/referencias';
$route['admin/crear_referencia'] = 'admin/crear_referencia';
$route['admin/actualizar_referencia'] = 'admin/actualizar_referencia';
$route['admin/eliminar_referencia/(:num)'] = 'admin/eliminar_referencia/$1';
$route['admin/ver_referencia/(:num)'] = 'admin/ver_referencia/$1';
$route['admin/editar_referencia/(:num)'] = 'admin/editar_referencia/$1';
$route['admin/usuarios'] = 'admin/usuarios';
$route['admin/usuarios/obtener/(:num)'] = 'admin/obtener_usuario/$1';
$route['admin/usuarios/ver/(:num)'] = 'admin/ver_usuario/$1';
$route['admin/usuarios/editar/(:num)'] = 'admin/editar_usuario/$1';
$route['admin/actualizar_usuario'] = 'admin/actualizar_usuario';
$route['admin/categorias'] = 'categoria';
$route['admin/categorias/crear'] = 'categoria/crear';
$route['admin/categorias/editar/(:num)'] = 'categoria/editar/$1';
$route['admin/categorias/eliminar/(:num)'] = 'categoria/eliminar/$1';
$route['admin/movimientos'] = 'admin/movimientos';

// Rutas de administrador (versión con 'administrador' para compatibilidad)
$route['administrador'] = 'admin/dashboard';
$route['administrador/dashboard'] = 'admin/dashboard';
$route['administrador/productos'] = 'admin/productos';
$route['administrador/usuarios'] = 'admin/usuarios';
$route['administrador/categorias'] = 'categoria';
$route['administrador/categorias/crear'] = 'categoria/crear';
$route['administrador/categorias/editar/(:num)'] = 'categoria/editar/$1';
$route['administrador/categorias/eliminar/(:num)'] = 'categoria/eliminar/$1';
$route['administrador/movimientos'] = 'admin/movimientos';

// Rutas de jefe
$route['jefe'] = 'jefe/index';
$route['jefe/dashboard'] = 'jefe/dashboard';
$route['jefe/productos'] = 'jefe/productos';
$route['jefe/ver_producto/(:num)'] = 'jefe/ver_producto/$1';
$route['jefe/reportes'] = 'jefe/reportes';
$route['jefe/perfil'] = 'jefe/perfil';

// Rutas de API
$route['api/categorias/select2'] = 'categoria/get_categorias_select2';

// Ruta de respaldo para dashboard genérico (redirige según rol)
$route['dashboard'] = 'dashboard/index';

// Ruta por defecto
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
