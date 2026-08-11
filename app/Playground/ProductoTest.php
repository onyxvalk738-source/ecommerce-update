<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use App\Config\Database;
use App\Models\Producto;
use App\Repositories\ProductoRepository;
use App\Services\ProductoService;
use DateTime;
use Exception;


// ======================================================
// CONEXIÓN
// ======================================================

$conexion = new Database(
    "localhost",
    "ECOMMERCE",
    "php",
    "mariyjuan123",
    "utf8mb4"
);

$repository = new ProductoRepository($conexion);
$service = new ProductoService($repository);


// ======================================================
// 1. PRUEBA GUARDAR PRODUCTO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "1. PRUEBA GUARDAR PRODUCTO" . PHP_EOL;
echo "========================================" . PHP_EOL;

$producto1 = new Producto(
    null,
    1,
    "Audifonos Logitech",
    new DateTime("2027-12-31"),
    "Audifonos gaming",
    "TEST001",
    85000,
    15,
    true
);

try {

    $service->guardar($producto1);

    echo "Producto guardado correctamente." . PHP_EOL;

} catch (Exception $e) {

    echo "Error: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// 2. GUARDAR SEGUNDO PRODUCTO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "2. GUARDAR SEGUNDO PRODUCTO" . PHP_EOL;
echo "========================================" . PHP_EOL;

$producto2 = new Producto(
    null,
    1,
    "Mouse Logitech",
    new DateTime("2027-12-31"),
    "Mouse gamer",
    "TEST002",
    50000,
    20,
    true
);

try {

    $service->guardar($producto2);

    echo "Segundo producto guardado correctamente." . PHP_EOL;

} catch (Exception $e) {

    echo "Error: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// 3. OBTENER PRODUCTO POR ID
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "3. PRUEBA OBTENER POR ID" . PHP_EOL;
echo "========================================" . PHP_EOL;

/*
 * Como los IDs son AUTO_INCREMENT,
 * buscamos los productos por su código.
 */

$productos = $repository->obtenerTodos();

$productoEncontrado1 = null;
$productoEncontrado2 = null;

foreach ($productos as $producto) {

    if ($producto->getCodigo() === "TEST001") {
        $productoEncontrado1 = $producto;
    }

    if ($producto->getCodigo() === "TEST002") {
        $productoEncontrado2 = $producto;
    }
}

if ($productoEncontrado1 !== null) {

    echo "Producto 1 encontrado:" . PHP_EOL;
    echo "ID: " . $productoEncontrado1->getId() . PHP_EOL;
    echo "Nombre: " . $productoEncontrado1->getNombre() . PHP_EOL;
    echo "Precio: " . $productoEncontrado1->getPrecio() . PHP_EOL;

} else {

    echo "No se encontró TEST001." . PHP_EOL;
}


if ($productoEncontrado2 !== null) {

    echo PHP_EOL;
    echo "Producto 2 encontrado:" . PHP_EOL;
    echo "ID: " . $productoEncontrado2->getId() . PHP_EOL;
    echo "Nombre: " . $productoEncontrado2->getNombre() . PHP_EOL;
    echo "Precio: " . $productoEncontrado2->getPrecio() . PHP_EOL;

} else {

    echo "No se encontró TEST002." . PHP_EOL;
}


// ======================================================
// 4. PRUEBA ACTUALIZAR
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "4. PRUEBA ACTUALIZAR" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($productoEncontrado1 !== null) {

    $productoEncontrado1->setNombre("Audifonos Logitech G Pro");
    $productoEncontrado1->setPrecio(95000);

    try {

        $service->actualizar($productoEncontrado1);

        echo "Producto actualizado correctamente." . PHP_EOL;

    } catch (Exception $e) {

        echo "Error actualizando: " . $e->getMessage() . PHP_EOL;
    }
}


// ======================================================
// 5. VERIFICAR ACTUALIZACIÓN
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "5. VERIFICAR ACTUALIZACIÓN" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($productoEncontrado1 !== null) {

    $productoActualizado = $service->obtenerPorId(
        $productoEncontrado1->getId()
    );

    echo "ID: " . $productoActualizado->getId() . PHP_EOL;
    echo "Nombre: " . $productoActualizado->getNombre() . PHP_EOL;
    echo "Precio: " . $productoActualizado->getPrecio() . PHP_EOL;
    echo "Unidades: " . $productoActualizado->getUnidades() . PHP_EOL;
}


// ======================================================
// 6. PRUEBA CÓDIGO DUPLICADO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "6. PRUEBA CÓDIGO DUPLICADO" . PHP_EOL;
echo "========================================" . PHP_EOL;

$productoDuplicado = new Producto(
    null,
    1,
    "Audifonos Razer",
    new DateTime("2027-12-31"),
    "Audifonos gaming",
    "TEST001",
    120000,
    5,
    true
);

try {

    $service->guardar($productoDuplicado);

    echo "ERROR: El producto se guardó aunque el código estaba repetido." . PHP_EOL;

} catch (Exception $e) {

    echo "Correcto: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// 7. PRUEBA NOMBRE VACÍO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "7. PRUEBA NOMBRE VACÍO" . PHP_EOL;
echo "========================================" . PHP_EOL;

$productoSinNombre = new Producto(
    null,
    1,
    "",
    new DateTime("2027-12-31"),
    "Producto de prueba",
    "TEST003",
    50000,
    10,
    true
);

try {

    $service->guardar($productoSinNombre);

    echo "ERROR: Se permitió guardar un producto sin nombre." . PHP_EOL;

} catch (Exception $e) {

    echo "Correcto: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// 8. PRUEBA PRECIO INVÁLIDO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "8. PRUEBA PRECIO INVÁLIDO" . PHP_EOL;
echo "========================================" . PHP_EOL;

$productoPrecioInvalido = new Producto(
    null,
    1,
    "Producto inválido",
    new DateTime("2027-12-31"),
    "Prueba",
    "TEST004",
    -5000,
    10,
    true
);

try {

    $service->guardar($productoPrecioInvalido);

    echo "ERROR: Se permitió un precio negativo." . PHP_EOL;

} catch (Exception $e) {

    echo "Correcto: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// 9. PRUEBA UNIDADES NEGATIVAS
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "9. PRUEBA UNIDADES NEGATIVAS" . PHP_EOL;
echo "========================================" . PHP_EOL;

$productoUnidadesInvalidas = new Producto(
    null,
    1,
    "Producto inválido",
    new DateTime("2027-12-31"),
    "Prueba",
    "TEST005",
    50000,
    -10,
    true
);

try {

    $service->guardar($productoUnidadesInvalidas);

    echo "ERROR: Se permitieron unidades negativas." . PHP_EOL;

} catch (Exception $e) {

    echo "Correcto: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// 10. PRUEBA SOFT DELETE
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "10. PRUEBA SOFT DELETE" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($productoEncontrado2 !== null) {

    $idProductoEliminar = $productoEncontrado2->getId();

    try {

        $service->eliminar($idProductoEliminar);

        echo "Producto eliminado correctamente." . PHP_EOL;

        /*
         * Intentamos buscarlo nuevamente.
         */

        $productoEliminado = $repository->obtenerPorId(
            $idProductoEliminar
        );

        if ($productoEliminado === null) {

            echo "Correcto: el producto ya no aparece en las consultas." . PHP_EOL;

        } else {

            echo "ERROR: el producto todavía aparece." . PHP_EOL;
        }

    } catch (Exception $e) {

        echo "Error eliminando: " . $e->getMessage() . PHP_EOL;
    }
}


// ======================================================
// FIN
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "FIN DE LAS PRUEBAS" . PHP_EOL;
echo "========================================" . PHP_EOL;