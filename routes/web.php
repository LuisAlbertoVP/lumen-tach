<?php

$router->post('login', 'UsuarioController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('modulos', 'ModuloController@getAll');
    $router->get('usuarios', 'UsuarioController@getAll');
    $router->get('usuarios/{id}', 'UsuarioController@getById');
    $router->post('usuarios', 'UsuarioController@insertOrUpdate');
    $router->post('usuarios/{id}', 'UsuarioController@setStatus');
    $router->delete('usuarios/{id}', 'UsuarioController@delete');
    $router->get('roles', 'RolController@getAll');
    $router->get('roles/{id}', 'RolController@getById');
    $router->post('roles', 'RolController@insertOrUpdate');
    $router->post('roles/{id}', 'RolController@setStatus');
    $router->delete('roles/{id}', 'RolController@delete');
    $router->get('proveedores', 'ProveedorController@getAll');
    $router->get('proveedores/{id}', 'ProveedorController@getById');
    $router->post('proveedores', 'ProveedorController@insertOrUpdate');
    $router->post('proveedores/{id}', 'ProveedorController@setStatus');
    $router->delete('proveedores/{id}', 'ProveedorController@delete');
    $router->get('marcas', 'MarcaController@getAll');
    $router->post('marcas', 'MarcaController@insertOrUpdate');
    $router->post('marcas/{id}', 'MarcaController@setStatus');
    $router->delete('marcas/{id}', 'MarcaController@delete');
    $router->get('categorias', 'CategoriaController@getAll');
    $router->post('categorias', 'CategoriaController@insertOrUpdate');
    $router->post('categorias/{id}', 'CategoriaController@setStatus');
    $router->delete('categorias/{id}', 'CategoriaController@delete');
    $router->get('repuestos', 'RepuestoController@getAll');
    $router->get('repuestos/{id}', 'RepuestoController@getById');
    $router->post('repuestos', 'RepuestoController@insertOrUpdate');
    $router->post('repuestos/{id}', 'RepuestoController@setStatus');
    $router->delete('repuestos/{id}', 'RepuestoController@delete');
    $router->get('ventas', 'VentaController@getAll');
    $router->get('ventas/{id}', 'VentaController@getById');
    $router->post('ventas', 'VentaController@insertOrUpdate');
});