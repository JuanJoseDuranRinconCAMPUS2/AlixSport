<?php
    require __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/routes/enrutamiento.php';

    use Dotenv\Dotenv;


    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();


    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");


    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    $router = require __DIR__ . '/routes/enrutamiento.php';


    $router->resolve();

?>
