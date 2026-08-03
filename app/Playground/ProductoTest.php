<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use App\Config\Database;
use App\Repositories\ProductoRepository;

$conexion = new Database(
    "localhost",
    "ECOMMERCE",
    "php",
    "mariyjuan123",
    "utf8mb4"
);

$repository = new ProductoRepository($conexion);

echo "===== PRUEBA OBTENER POR ID =====" . PHP_EOL;

$producto = $repository->obtenerPorId(1);

if ($producto === null) {
    echo "Producto no encontrado." . PHP_EOL;
    exit;
}

echo "ID: " . $producto->getId() . PHP_EOL;
echo "Nombre: " . $producto->getNombre() . PHP_EOL;
echo "Precio: " . $producto->getPrecio() . PHP_EOL;

echo PHP_EOL;

echo "===== ACTUALIZANDO =====" . PHP_EOL;

$producto->setNombre("Teclado Logitech");
$producto->setPrecio(180000);

$repository->actualizar($producto);

echo "Producto actualizado correctamente." . PHP_EOL;

echo PHP_EOL;
echo "===== VERIFICANDO CAMBIOS =====" . PHP_EOL;

$productoActualizado = $repository->obtenerPorId(1);

if ($productoActualizado !== null) {
    echo "ID:" . $productoActualizado->getId() . PHP_EOL;
    echo "Nombre: " . $productoActualizado->getNombre() . PHP_EOL;
    echo "Precio: " . $productoActualizado->getPrecio() . PHP_EOL;
    echo "Unidades: " . $productoActualizado->getUnidades() . PHP_EOL;
    }