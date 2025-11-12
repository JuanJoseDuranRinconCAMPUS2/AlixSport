<?php
    require __DIR__ . '/vendor/autoload.php';

    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $host = $_ENV['HOSTNAME'];
    $port = $_ENV['PORT_BACKEND'];
    $root = __DIR__ . '/api';


    if (!is_dir($root)) {
        echo "Error: La carpeta '$root' no existe.\n";
        exit(1);
    }


    echo "Iniciando servidor PHP...\n";
    echo "Raíz del servidor: $root\n";
    echo "URL: http://$host:$port\n";
    echo "Presiona Ctrl + C para detenerlo\n\n";


    $command = "php -S $host:$port -t $root";
    passthru($command);

?>