<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use App\Config\Database;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Repositories\DetallePedidoRepository;
use App\Repositories\PedidoRepository;
use App\Repositories\ProductoRepository;
use App\Services\DetallePedidoService;
use App\Services\PedidoService;
use DateTime;
use Exception;


// ======================================================
// CONEXIÓN Y DEPENDENCIAS
// ======================================================

$conexion = new Database(
    "localhost",
    "ECOMMERCE",
    "php",
    "mariyjuan123",
    "utf8mb4"
);

$pedidoRepository = new PedidoRepository($conexion);
$detalleRepository = new DetallePedidoRepository($conexion);
$productoRepository = new ProductoRepository($conexion);

$pedidoService = new PedidoService($pedidoRepository, $detalleRepository);
$detalleService = new DetallePedidoService(
    $detalleRepository,
    $productoRepository,
    $pedidoService
);


function encontrarPedidoDeCliente(array $pedidos, int $idCliente): ?Pedido
{
    foreach ($pedidos as $pedido) {
        if ($pedido->getIdCliente() === $idCliente) {
            return $pedido;
        }
    }

    return null;
}


function encontrarProducto(array $productos, string $codigo): ?Producto
{
    foreach ($productos as $producto) {
        if ($producto->getCodigo() === $codigo) {
            return $producto;
        }
    }

    return null;
}


// ======================================================
// 1. PRUEBA GUARDAR PEDIDO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "1. PRUEBA GUARDAR PEDIDO" . PHP_EOL;
echo "========================================" . PHP_EOL;

$pedido1 = new Pedido(
    null,
    9001,
    new DateTime("2026-08-16"),
    "",
    0
);

try {

    $pedidoService->guardar($pedido1);

    $pedidos = $pedidoRepository->obtenerTodos();

    $pedidoGuardado = encontrarPedidoDeCliente($pedidos, 9001);

    if ($pedidoGuardado === null) {
        echo "ERROR: no se encontró el pedido guardado." . PHP_EOL;
    } else {
        echo "Pedido guardado correctamente." . PHP_EOL;
        echo "ID: " . $pedidoGuardado->getId() . PHP_EOL;
        echo "Estado por defecto: " . $pedidoGuardado->getEstado() . PHP_EOL;
        echo "Total inicial: " . $pedidoGuardado->getTotal() . PHP_EOL;
    }

} catch (Exception $e) {

    echo "Error: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// 2. PRUEBA AGREGAR DETALLES AL PEDIDO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "2. PRUEBA AGREGAR DETALLES" . PHP_EOL;
echo "========================================" . PHP_EOL;

$productos = $productoRepository->obtenerTodos();

$productoTeclado = encontrarProducto($productos, "TEC001");
$productoMouse = encontrarProducto($productos, "MOU0001");

if ($pedidoGuardado !== null && $productoTeclado !== null && $productoMouse !== null) {

    $idPedido = $pedidoGuardado->getId();

    $detalle1 = new DetallePedido(
        null,
        $idPedido,
        $productoTeclado->getId(),
        2,
        180000,
        0
    );

    $detalle2 = new DetallePedido(
        null,
        $idPedido,
        $productoMouse->getId(),
        3,
        30000,
        0
    );

    try {

        $detalleService->guardar($detalle1);
        $detalleService->guardar($detalle2);

        echo "Detalles guardados correctamente." . PHP_EOL;
        echo "Subtotal 1 (2 x 180000): " . $detalle1->getSubtotal() . PHP_EOL;
        echo "Subtotal 2 (3 x 30000): " . $detalle2->getSubtotal() . PHP_EOL;

    } catch (Exception $e) {

        echo "Error: " . $e->getMessage() . PHP_EOL;
    }

} else {

    echo "No se pudo preparar la prueba: faltan pedido o productos." . PHP_EOL;
}


// ======================================================
// 3. PRUEBA CÁLCULO DEL TOTAL DEL PEDIDO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "3. PRUEBA CÁLCULO DEL TOTAL" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($pedidoGuardado !== null) {

    $idPedido = $pedidoGuardado->getId();

    $pedidoRecalculado = $pedidoService->obtenerPorId($idPedido);

    $totalEsperado = (2 * 180000) + (3 * 30000);

    echo "Total esperado: " . $totalEsperado . PHP_EOL;
    echo "Total calculado: " . $pedidoRecalculado->getTotal() . PHP_EOL;

    if ($pedidoRecalculado->getTotal() === (float) $totalEsperado) {
        echo "Correcto: el total coincide." . PHP_EOL;
    } else {
        echo "ERROR: el total no coincide." . PHP_EOL;
    }
}


// ======================================================
// 4. PRUEBA CAMBIO DE ESTADO VÁLIDO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "4. PRUEBA CAMBIO DE ESTADO VÁLIDO" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($pedidoGuardado !== null) {

    $idPedido = $pedidoGuardado->getId();

    try {

        $pedidoService->cambiarEstado($idPedido, Pedido::ESTADO_PROCESADO);

        $pedidoActualizado = $pedidoService->obtenerPorId($idPedido);

        echo "Estado actual: " . $pedidoActualizado->getEstado() . PHP_EOL;

        if ($pedidoActualizado->getEstado() === Pedido::ESTADO_PROCESADO) {
            echo "Correcto: transición pendiente -> procesado permitida." . PHP_EOL;
        } else {
            echo "ERROR: el estado no cambió." . PHP_EOL;
        }

    } catch (Exception $e) {

        echo "Error: " . $e->getMessage() . PHP_EOL;
    }
}


// ======================================================
// 5. PRUEBA CAMBIO DE ESTADO INVÁLIDO
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "5. PRUEBA CAMBIO DE ESTADO INVÁLIDO" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($pedidoGuardado !== null) {

    $idPedido = $pedidoGuardado->getId();

    try {

        $pedidoService->cambiarEstado($idPedido, Pedido::ESTADO_PENDIENTE);

        echo "ERROR: se permitió volver de procesado a pendiente." . PHP_EOL;

    } catch (Exception $e) {

        echo "Correcto: " . $e->getMessage() . PHP_EOL;
    }
}


// ======================================================
// 6. PRUEBA ACTUALIZAR DETALLE Y RECALCULAR TOTAL
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "6. PRUEBA ACTUALIZAR DETALLE" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($pedidoGuardado !== null) {

    $idPedido = $pedidoGuardado->getId();

    $detalles = $detalleService->obtenerPorPedido($idPedido);

    if (count($detalles) > 0) {

        $detalle = $detalles[0];

        $detalle->setCantidad(5);
        $detalle->setPrecioUnitario(100000);

        try {

            $detalleService->actualizar($detalle);

            $pedidoRecalculado = $pedidoService->obtenerPorId($idPedido);

            $totalEsperado = (5 * 100000) + (3 * 30000);

            echo "Nuevo subtotal 1: " . $detalle->getSubtotal() . PHP_EOL;
            echo "Total esperado: " . $totalEsperado . PHP_EOL;
            echo "Total calculado: " . $pedidoRecalculado->getTotal() . PHP_EOL;

        } catch (Exception $e) {

            echo "Error: " . $e->getMessage() . PHP_EOL;
        }

    } else {

        echo "No hay detalles para actualizar." . PHP_EOL;
    }
}


// ======================================================
// 7. PRUEBA ELIMINAR DETALLE Y RECALCULAR TOTAL
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "7. PRUEBA ELIMINAR DETALLE" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($pedidoGuardado !== null) {

    $idPedido = $pedidoGuardado->getId();

    $detalles = $detalleService->obtenerPorPedido($idPedido);

    if (count($detalles) > 1) {

        $detalleAEliminar = $detalles[1];

        try {

            $detalleService->eliminar($detalleAEliminar->getId());

            $pedidoRecalculado = $pedidoService->obtenerPorId($idPedido);

            $totalEsperado = 5 * 100000;

            echo "Total esperado: " . $totalEsperado . PHP_EOL;
            echo "Total calculado: " . $pedidoRecalculado->getTotal() . PHP_EOL;

        } catch (Exception $e) {

            echo "Error: " . $e->getMessage() . PHP_EOL;
        }

    } else {

        echo "No hay suficientes detalles para eliminar." . PHP_EOL;
    }
}


// ======================================================
// 8. PRUEBA DETALLE CON PRODUCTO INEXISTENTE
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "8. PRUEBA PRODUCTO INEXISTENTE" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($pedidoGuardado !== null) {

    $detalleInvalido = new DetallePedido(
        null,
        $pedidoGuardado->getId(),
        999999,
        1,
        10000,
        0
    );

    try {

        $detalleService->guardar($detalleInvalido);

        echo "ERROR: se guardó un detalle con producto inexistente." . PHP_EOL;

    } catch (Exception $e) {

        echo "Correcto: " . $e->getMessage() . PHP_EOL;
    }
}


// ======================================================
// 9. PRUEBA DETALLE CON CANTIDAD INVÁLIDA
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "9. PRUEBA CANTIDAD INVÁLIDA" . PHP_EOL;
echo "========================================" . PHP_EOL;

if ($pedidoGuardado !== null && $productoTeclado !== null) {

    $detalleInvalido = new DetallePedido(
        null,
        $pedidoGuardado->getId(),
        $productoTeclado->getId(),
        0,
        10000,
        0
    );

    try {

        $detalleService->guardar($detalleInvalido);

        echo "ERROR: se permitió una cantidad menor o igual a cero." . PHP_EOL;

    } catch (Exception $e) {

        echo "Correcto: " . $e->getMessage() . PHP_EOL;
    }
}


// ======================================================
// 10. PRUEBA PEDIDO INEXISTENTE
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "10. PRUEBA PEDIDO INEXISTENTE" . PHP_EOL;
echo "========================================" . PHP_EOL;

try {

    $pedidoService->obtenerPorId(999999);

    echo "ERROR: se obtuvo un pedido inexistente." . PHP_EOL;

} catch (Exception $e) {

    echo "Correcto: " . $e->getMessage() . PHP_EOL;
}


// ======================================================
// FIN
// ======================================================

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "FIN DE LAS PRUEBAS" . PHP_EOL;
echo "========================================" . PHP_EOL;