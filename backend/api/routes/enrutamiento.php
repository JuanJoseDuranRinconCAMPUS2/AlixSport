<?php

    require_once __DIR__ . '/../core/Router.php';
    require_once __DIR__ . '/../controllers/loginUserController.php';
    require_once __DIR__ . '/../controllers/productosController.php';
    require_once __DIR__ . '/../controllers/carritoController.php';

    $router = new Router();

    // Rutas Productos
    $router->get('/productos', 'productosController@getProductos');
    $router->get('/productosById', 'productosController@getProductoById');
    $router->post('/productos', 'productosController@postProducto');
    $router->put('/productos', 'productosController@putProducto');
    $router->delete('/productos', 'productosController@deleteProducto');


    // Rutas Carrito
    $router->get('/carritos', 'carritoController@getCarrito');
    $router->get('/carritosDetail', 'carritoController@getCarritoDetalles');
    $router->get('/carritosById', 'carritoController@getCarritoById');
    $router->get('/DetallesByUser', 'carritoController@getDetallesByUser');
    $router->get('/totalCarritoUser', 'carritoController@getTotalCarrito');
    $router->post('/carritos', 'carritoController@postCarrito');
    $router->delete('/carritos', 'carritoController@deleteDetalleCarrito');
    $router->delete('/vaciarCarrito', 'carritoController@vaciarCarrito');

    //Rutas Login
    $router->get('/hola', 'loginUserController@saludar');
    $router->post('/registrarUsuario', 'loginUserController@registrarUsuario');
    $router->post('/loginUsuario', 'loginUserController@loginUsuario');
    $router->post('/enviarCodigoRecuperacion', 'loginUserController@sendCodigoRep');
    $router->post('/cambiarContrasenia', 'loginUserController@changePassword');
    
    return $router;
?>