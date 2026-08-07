<?php

require_once "vendor/autoload.php";

use App\Config\Database;
use App\Models\Producto;
use App\Repositories\ProductoRepository;
use App\Services\ProductoService;

$conexion = new Database(
    "localhost",
    "ECOMMERCE",
    "php",
    "mariyjuan123",
    "utf8mb4"
);

$repository = new ProductoRepository($conexion);

$service = new ProductoService($repository);

$producto = new Producto(
    null,
    1,
    "Teclado",
    new DateTime("2027-12-31"),
    "Teclado mecanico",
    "TEC001",
    150000.00,
    10,
    true
);

// $repository->guardar($producto);

try{
    $service->guardar($producto);

    echo "Producto guardado correctamente";

}catch(Exception $e){
    echo $e->getMessage();
}

echo "Producto guardado correctamente";

echo PHP_EOL;

$producto = $service->obtenerPorId(1);

if ($producto !== null){
    echo $producto->getNombre();
}


