<?php

$router->post('api/login', 'UsuarioController@login');
$router->post('api/usuarios/request', 'UsuarioController@insert');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('api/usuarios/{id}/roles', 'UsuarioController@getRolUser');
    $router->get('api/usuarios/form', 'UsuarioController@getForm');
    $router->post('api/usuarios/all', 'UsuarioController@getAll');
    $router->post('api/usuarios/{id}', 'UsuarioController@setStatus');
    $router->post('api/usuarios', 'UsuarioController@insertOrUpdate');
    $router->delete('api/usuarios/{id}', 'UsuarioController@delete');
    $router->post('api/roles/all', 'RolController@getAll');
    $router->post('api/roles/{id}', 'RolController@setStatus');
    $router->post('api/roles', 'RolController@insertOrUpdate');
    $router->delete('api/roles/{id}', 'RolController@delete');
    $router->get('api/proveedores/{id}', 'ProveedorController@getById');
    $router->post('api/proveedores/all', 'ProveedorController@getAll');
    $router->post('api/proveedores/{id}', 'ProveedorController@setStatus');
    $router->post('api/proveedores', 'ProveedorController@insertOrUpdate');
    $router->delete('api/proveedores/{id}', 'ProveedorController@delete');
    $router->post('api/marcas/all', 'MarcaController@getAll');
    $router->post('api/marcas/{id}', 'MarcaController@setStatus');
    $router->post('api/marcas', 'MarcaController@insertOrUpdate');
    $router->delete('api/marcas/{id}', 'MarcaController@delete');
    $router->post('api/categorias/all', 'CategoriaController@getAll');
    $router->post('api/categorias/{id}', 'CategoriaController@setStatus');
    $router->post('api/categorias', 'CategoriaController@insertOrUpdate');
    $router->delete('api/categorias/{id}', 'CategoriaController@delete');
    $router->get('api/repuestos/form', 'RepuestoController@getForm');
    $router->post('api/repuestos/all', 'RepuestoController@getAll');
    $router->post('api/repuestos/{id}', 'RepuestoController@setStatus');
    $router->post('api/repuestos', 'RepuestoController@insertOrUpdate');
    $router->delete('api/repuestos/{id}', 'RepuestoController@delete');
    $router->get('api/ventas', 'VentaController@getAll');
    $router->get('api/ventas/{id}', 'VentaController@getById');
    $router->post('api/ventas', 'VentaController@insertOrUpdate');
});