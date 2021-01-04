<?php

$router->post('api/login', 'PublicController@login');
$router->post('api/cuenta', 'PublicController@insert');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('api/cuenta/{id}', 'PrincipalController@getById');
    $router->post('api/cuenta', 'PrincipalController@update');
    $router->get('api/usuarios/{id}/roles', 'UsuarioController@getRolUser');
    $router->get('api/usuarios/form', 'UsuarioController@getForm');
    $router->post('api/usuarios/all', 'UsuarioController@getAll');
    $router->post('api/usuarios', 'UsuarioController@insertOrUpdate');
    $router->post('api/usuarios/{id}/status', 'UsuarioController@setStatus');
    $router->post('api/usuarios/{id}/delete', 'UsuarioController@delete');
    $router->post('api/roles/all', 'RolController@getAll');
    $router->post('api/roles', 'RolController@insertOrUpdate');
    $router->post('api/roles/{id}/status', 'RolController@setStatus');
    $router->post('api/roles/{id}/delete', 'RolController@delete');
    $router->post('api/proveedores/all', 'ProveedorController@getAll');
    $router->post('api/proveedores', 'ProveedorController@insertOrUpdate');
    $router->post('api/proveedores/{id}/status', 'ProveedorController@setStatus');
    $router->post('api/proveedores/{id}/delete', 'ProveedorController@delete');
    $router->post('api/marcas/all', 'MarcaController@getAll');
    $router->post('api/marcas', 'MarcaController@insertOrUpdate');
    $router->post('api/marcas/{id}/status', 'MarcaController@setStatus');
    $router->post('api/marcas/{id}/delete', 'MarcaController@delete');
    $router->post('api/categorias/all', 'CategoriaController@getAll');
    $router->post('api/categorias', 'CategoriaController@insertOrUpdate');
    $router->post('api/categorias/{id}/status', 'CategoriaController@setStatus');
    $router->post('api/categorias/{id}/delete', 'CategoriaController@delete');
    $router->get('api/repuestos/form', 'RepuestoController@getForm');
    $router->post('api/repuestos/all', 'RepuestoController@getAll');
    $router->post('api/repuestos', 'RepuestoController@insertOrUpdate');
    $router->post('api/repuestos/{id}/status', 'RepuestoController@setStatus');
    $router->post('api/repuestos/{id}/delete', 'RepuestoController@delete');
    $router->get('api/ventas', 'VentaController@getAll');
    $router->get('api/ventas/{id}', 'VentaController@getById');
    $router->post('api/ventas', 'VentaController@insertOrUpdate');
});