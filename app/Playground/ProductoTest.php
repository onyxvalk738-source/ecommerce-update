<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use App\Config\Database;
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

$repository = new ProductoService($repository);

echo "===== PRUEBA OBTENER POR ID =====" . PHP_EOL;

$producto1 = $repository->obtenerPorId(1);
$producto2 = $repository->obtenerPorId(2);

if ($producto1 === null || $producto2 === null) {
    echo "Uno de los productos no existe." . PHP_EOL;
    exit;
}

echo PHP_EOL;

echo "=== Producto 1 ===". PHP_EOL;

echo "ID: " . $producto1->getId() . PHP_EOL;
echo "Nombre: " . $producto1->getNombre() . PHP_EOL;
echo "Precio: " . $producto1->getPrecio() . PHP_EOL;

echo PHP_EOL;

echo "=== Producto 2 ===" . PHP_EOL;

echo "ID: " . $producto2->getId() . PHP_EOL;
echo "Nombre: " . $producto2->getNombre() . PHP_EOL;
echo "Precio: " . $producto2->getPrecio() . PHP_EOL;

echo "===== ACTUALIZANDO =====" . PHP_EOL;

$producto1->setNombre("Teclado Logitech");
$producto1->setPrecio(180000);

$producto2->setNombre("Mouse Razer");
$producto2->setPrecio(20000);

$service->actualizar($producto1);
$service->actualizar($producto2);

echo "Producto actualizado correctamente." . PHP_EOL;

echo PHP_EOL;

echo "===== VERIFICANDO CAMBIOS =====" . PHP_EOL;

$productoActualizado1 = $repository->obtenerPorId(1);
$productoActualizado2 = $repository->obtenerPorId(2);

echo "===== Producto 1 Actualizado" . PHP_EOL;

if ($productoActualizado1 !== null) {
    echo "ID:" . $productoActualizado1->getId() . PHP_EOL;
    echo "Nombre: " . $productoActualizado1->getNombre() . PHP_EOL;
    echo "Precio: " . $productoActualizado1->getPrecio() . PHP_EOL;
    echo "Unidades: " . $productoActualizado1->getUnidades() . PHP_EOL;
    }

echo "===== Producto 2 Actualizado =====" . PHP_EOL;

if ($productoActualizado2 !== null) {
    echo "ID:" . $productoActualizado2->getId() . PHP_EOL;
    echo "Nombre: " . $productoActualizado2->getNombre() . PHP_EOL;
    echo "Precio: " . $productoActualizado2->getPrecio() . PHP_EOL;
    echo "Unidades: " . $productoActualizado2->getUnidades() . PHP_EOL;
    }
