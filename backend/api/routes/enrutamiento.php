<?php

    require_once __DIR__ . '/../core/Router.php';
    require_once __DIR__ . '/../controllers/loginUserController.php';
    require_once __DIR__ . '/../controllers/productosController.php';
    require_once __DIR__ . '/../controllers/carritoController.php';
    require_once __DIR__ . '/../controllers/saboresController.php';
    require_once __DIR__ . '/../controllers/categoriasController.php';

    $router = new Router();

    // Rutas Productos
    $router->get('/productos', 'productosController@getProductos');
    $router->get('/productosById', 'productosController@getProductoById');
    $router->post('/productos', 'productosController@postProducto');
    $router->post('/editarProductos', 'productosController@putProducto');
    $router->delete('/productos', 'productosController@deleteProducto');


    // Rutas Carrito
    $router->get('/carritos', 'carritoController@getCarrito');
    $router->get('/carritosDetail', 'carritoController@getCarritoDetalles');
    $router->post('/CantidadProductos', 'carritoController@getCantidadProductos');
    $router->post('/carritosById', 'carritoController@getCarritoById');
    $router->post('/DetallesByUser', 'carritoController@getDetallesByUser');
    $router->post('/totalCarritoUser', 'carritoController@getTotalCarrito');
    $router->post('/cambiarCantidadCarrito', 'carritoController@updateCantidadCarrito');
    $router->post('/carritos', 'carritoController@postCarrito');
    $router->delete('/carritos', 'carritoController@deleteDetalleCarrito');
    $router->delete('/vaciarCarrito', 'carritoController@vaciarCarrito');
    $router->post('/generarFactura', 'carritoController@generarFacturaPDF');

    // Rutas Sabores
    $router->get('/sabores', 'saboresController@getSabores');
    $router->get('/saboresById', 'saboresController@getSaborById');
    $router->post('/sabores', 'saboresController@postSabor');
    $router->put('/sabores', 'saboresController@putSabor');
    $router->delete('/sabores', 'saboresController@deleteSabor');

    // Rutas Sabores
    $router->get('/categorias', 'categoriasController@getCategorias');
    $router->get('/categoriasById', 'categoriasController@getCategoriaById');
    $router->post('/categorias', 'categoriasController@postCategoria');
    $router->put('/categorias', 'categoriasController@putCategoria');
    $router->delete('/categorias', 'categoriasController@deleteCategoria');

    //Rutas Login
    $router->get('/hola', 'loginUserController@saludar');
    $router->post('/registrarUsuario', 'loginUserController@registrarUsuario');
    $router->post('/loginUsuario', 'loginUserController@loginUsuario');
    $router->post('/enviarCodigoRecuperacion', 'loginUserController@sendCodigoRep');
    $router->post('/cambiarContrasenia', 'loginUserController@changePassword');
    
    return $router;
?>