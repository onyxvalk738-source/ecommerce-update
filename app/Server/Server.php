<?php

require __DIR__ . '/../../vendor/autoload.php';

use Amp\Http\HttpStatus;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Psr\Log\NullLogger;

use App\Config\Database;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Repositories\DetallePedidoRepository;
use App\Repositories\PedidoRepository;
use App\Repositories\ProductoRepository;
use App\Services\DetallePedidoService;
use App\Services\PedidoService;
use App\Services\ProductoService;

use function Amp\trapSignal;


/*
|--------------------------------------------------------------------------
| Dependencias
|--------------------------------------------------------------------------
*/

$database = new Database(
    "localhost",
    "ECOMMERCE",
    "php",
    "mariyjuan123",
    "utf8mb4"
);

$repository = new ProductoRepository($database);
$service = new ProductoService($repository);

$pedidoRepository = new PedidoRepository($database);
$detalleRepository = new DetallePedidoRepository($database);

$pedidoService = new PedidoService($pedidoRepository, $detalleRepository);
$detalleService = new DetallePedidoService($detalleRepository, $repository, $pedidoService);


/*
|--------------------------------------------------------------------------
| Funciones auxiliares
|--------------------------------------------------------------------------
*/

function respuestaJson(
    mixed $data,
    int $status = HttpStatus::OK
): Response {

    return new Response(
        status: $status,
        headers: [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => 'http://127.0.0.1:5500',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
        ],
        body: json_encode($data)
    );
}


function productoArray(Producto $producto): array
{
    return [
        'id' => $producto->getId(),
        'idCategoria' => $producto->getIdCategoria(),
        'nombre' => $producto->getNombre(),
        'fechaVencimiento' => $producto->getFechaVencimiento()->format('Y-m-d'),
        'informacion' => $producto->getInformacion(),
        'codigo' => $producto->getCodigo(),
        'precio' => $producto->getPrecio(),
        'unidades' => $producto->getUnidades(),
        'estado' => $producto->getEstado()
    ];
}

function pedidoArray(Pedido $pedido): array
{
    return [
        'id' => $pedido->getId(),
        'idCliente' => $pedido->getIdCliente(),
        'fechaPedido' => $pedido->getFechaPedido()->format('Y-m-d'),
        'estado' => $pedido->getEstado(),
        'total' => $pedido->getTotal()
    ];
}

function detallePedidoArray(DetallePedido $detallePedido): array
{
    return [
        'id' => $detallePedido->getId(),
        'idPedido' => $detallePedido->getIdPedido(),
        'idProducto' => $detallePedido->getIdProducto(),
        'cantidad' => $detallePedido->getCantidad(),
        'precio' => $detallePedido->getPrecioUnitario(),
        'subtotal' => $detallePedido->getSubtotal()
    ];
}


function obtenerIdDesdeRuta(string $path): ?int
{
    if (preg_match('#^/productos/(\d+)$#', $path, $matches)) {
        return (int) $matches[1];
    }

    return null;
}

function obtenerIdPedidoDesdeRuta(string $path): ?int
{
    if (preg_match('#^/pedidos/(\d+)$#', $path, $matches)) {
        return (int) $matches[1];
    }

    return null;
}

function obtenerIdDetalleDesdeRuta(string $path): ?int
{
    if (preg_match('#^/detalle-pedidos/(\d+)$#', $path, $matches)) {
        return (int) $matches[1];
    }

    return null;
}


/*
|--------------------------------------------------------------------------
| Request Handler
|--------------------------------------------------------------------------
*/

$requestHandler = new ClosureRequestHandler(

    function (Request $request) use ($service, $repository, $pedidoService, $detalleService, $pedidoRepository, $detalleRepository): Response {

        $method = $request->getMethod();
        $path = $request->getUri()->getPath();


        /*
        |--------------------------------------------------------------------------
        | CORS
        |--------------------------------------------------------------------------
        */

        if ($method === 'OPTIONS') {

            return new Response(
                status: HttpStatus::NO_CONTENT,
                headers: [
                    'Access-Control-Allow-Origin' => 'http://127.0.0.1:5500',
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
                ]
            );
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | GET /productos
            |--------------------------------------------------------------------------
            */

            if ($method === 'GET' && $path === '/productos') {

                $productos = $repository->obtenerTodos();

                $resultado = [];

                foreach ($productos as $producto) {
                    $resultado[] = productoArray($producto);
                }

                return respuestaJson([
                    'success' => true,
                    'data' => $resultado
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET /pedidos
            |--------------------------------------------------------------------------
            */

            if ($method === 'GET' && $path === '/pedidos') {

                $pedidos = $pedidoRepository->obtenerTodos();

                $resultado = [];

                foreach ($pedidos as $pedido) {
                    $resultado[] = pedidoArray($pedido);
                }

                return respuestaJson([
                    'success' => true,
                    'data' => $resultado
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET /detalle-pedidos
            |--------------------------------------------------------------------------
            */

            if ($method === 'GET' && $path === '/detalle-pedidos') {

                $detalles = $detalleRepository->obtenerTodos();

                $resultado = [];

                foreach ($detalles as $detalle) {
                    $resultado[] = detallePedidoArray($detalle);
                }

                return respuestaJson([
                    'success' => true,
                    'data' => $resultado
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | GET /productos/{id}
            |--------------------------------------------------------------------------
            */

            if ($method === 'GET') {

                $id = obtenerIdDesdeRuta($path);

                if ($id !== null) {

                    $producto = $service->obtenerPorId($id);

                    return respuestaJson([
                        'success' => true,
                        'data' => productoArray($producto)
                    ]);
                }

                $id = obtenerIdPedidoDesdeRuta($path);

                if ($id !== null) {

                    $pedido = $pedidoService->obtenerPorId($id);

                    return respuestaJson([
                        'success' => true,
                        'data' => pedidoArray($pedido)
                    ]);
                }

                $id = obtenerIdDetalleDesdeRuta($path);

                if ($id !== null) {

                    $detalle = $detalleService->obtenerPorId($id);

                    return respuestaJson([
                        'success' => true,
                        'data' => detallePedidoArray($detalle)
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | POST /productos
            |--------------------------------------------------------------------------
            */

            if ($method === 'POST' && $path === '/productos') {

                $body = $request->getBody()->buffer();

                $datos = json_decode($body, true);

                if (!is_array($datos)) {

                    return respuestaJson([
                        'success' => false,
                        'message' => 'JSON inválido'
                    ], HttpStatus::BAD_REQUEST);
                }


                $producto = new Producto(
                    null,
                    isset($datos['idCategoria'])
                        ? (int) $datos['idCategoria']
                        : null,

                    $datos['nombre'] ?? '',

                    new DateTime(
                        $datos['fechaVencimiento'] ?? 'now'
                    ),

                    $datos['informacion'] ?? '',

                    $datos['codigo'] ?? '',

                    (float) ($datos['precio'] ?? 0),

                    (int) ($datos['unidades'] ?? 0),

                    (bool) ($datos['estado'] ?? true)
                );


                $service->guardar($producto);


                return respuestaJson([
                    'success' => true,
                    'message' => 'Producto creado correctamente'
                ], HttpStatus::CREATED);
            }


            /*
            |--------------------------------------------------------------------------
            | POST /pedidos
            |--------------------------------------------------------------------------
            */

            if ($method === 'POST' && $path === '/pedidos') {

                $body = $request->getBody()->buffer();

                $datos = json_decode($body, true);

                if (!is_array($datos)) {

                    return respuestaJson([
                        'success' => false,
                        'message' => 'JSON inválido'
                    ], HttpStatus::BAD_REQUEST);
                }


                $pedido = new Pedido(
                    null,

                    isset($datos['idCliente'])
                        ? (int) $datos['idCliente']
                        : null,

                    new DateTime(
                        $datos['fechaPedido'] ?? 'now'
                    ),

                    $datos['estado'] ?? '',

                    (float) ($datos['total'] ?? 0)
                );


                $pedidoService->guardar($pedido);


                return respuestaJson([
                    'success' => true,
                    'message' => 'Pedido creado correctamente'
                ], HttpStatus::CREATED);
            }


            /*
            |--------------------------------------------------------------------------
            | POST /detalle-pedidos
            |--------------------------------------------------------------------------
            */

            if ($method === 'POST' && $path === '/detalle-pedidos') {

                $body = $request->getBody()->buffer();

                $datos = json_decode($body, true);

                if (!is_array($datos)) {

                    return respuestaJson([
                        'success' => false,
                        'message' => 'JSON inválido'
                    ], HttpStatus::BAD_REQUEST);
                }


                $detallePedido = new DetallePedido(
                    null,

                    isset($datos['idPedido'])
                        ? (int) $datos['idPedido']
                        : null,

                    isset($datos['idProducto'])
                        ? (int) $datos['idProducto']
                        : null,

                    (int) ($datos['cantidad'] ?? 0),

                    (float) ($datos['precio'] ?? 0),

                    (float) ($datos['subtotal'] ?? 0)
                );


                $detalleService->guardar($detallePedido);


                return respuestaJson([
                    'success' => true,
                    'message' => 'Detalle del pedido creado correctamente'
                ], HttpStatus::CREATED);
            }


            /*
            |--------------------------------------------------------------------------
            | PUT /productos/{id}
            |--------------------------------------------------------------------------
            */

            if ($method === 'PUT') {

                $id = obtenerIdDesdeRuta($path);

                if ($id !== null) {

                    $body = $request->getBody()->buffer();

                    $datos = json_decode($body, true);

                    if (!is_array($datos)) {

                        return respuestaJson([
                            'success' => false,
                            'message' => 'JSON inválido'
                        ], HttpStatus::BAD_REQUEST);
                    }


                    $producto = new Producto(
                        $id,

                        isset($datos['idCategoria'])
                            ? (int) $datos['idCategoria']
                            : null,

                        $datos['nombre'] ?? '',

                        new DateTime(
                            $datos['fechaVencimiento'] ?? 'now'
                        ),

                        $datos['informacion'] ?? '',

                        $datos['codigo'] ?? '',

                        (float) ($datos['precio'] ?? 0),

                        (int) ($datos['unidades'] ?? 0),

                        (bool) ($datos['estado'] ?? true)
                    );


                    $service->actualizar($producto);


                    return respuestaJson([
                        'success' => true,
                        'message' => 'Producto actualizado correctamente'
                    ]);
                }

                $id = obtenerIdPedidoDesdeRuta($path);

                if ($id !== null) {

                    $body = $request->getBody()->buffer();

                    $datos = json_decode($body, true);

                    if (!is_array($datos)) {

                        return respuestaJson([
                            'success' => false,
                            'message' => 'JSON inválido'
                        ], HttpStatus::BAD_REQUEST);
                    }


                    $pedido = new Pedido(
                        $id,

                        isset($datos['idCliente'])
                            ? (int) $datos['idCliente']
                            : null,

                        new DateTime(
                            $datos['fechaPedido'] ?? 'now'
                        ),

                        $datos['estado'] ?? '',

                        (float) ($datos['total'] ?? 0)
                    );


                    $pedidoService->actualizar($pedido);


                    return respuestaJson([
                        'success' => true,
                        'message' => 'Pedido actualizado correctamente'
                    ]);
                }

                $id = obtenerIdDetalleDesdeRuta($path);

                if ($id !== null) {

                    $body = $request->getBody()->buffer();

                    $datos = json_decode($body, true);

                    if (!is_array($datos)) {

                        return respuestaJson([
                            'success' => false,
                            'message' => 'JSON inválido'
                        ], HttpStatus::BAD_REQUEST);
                    }


                    $detallePedido = new DetallePedido(
                        $id,

                        isset($datos['idPedido'])
                            ? (int) $datos['idPedido']
                            : null,

                        isset($datos['idProducto'])
                            ? (int) $datos['idProducto']
                            : null,

                        (int) ($datos['cantidad'] ?? 0),

                        (float) ($datos['precio'] ?? 0),

                        (float) ($datos['subtotal'] ?? 0)
                    );


                    $detalleService->actualizar($detallePedido);


                    return respuestaJson([
                        'success' => true,
                        'message' => 'Detalle del pedido actualizado correctamente'
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | DELETE /productos/{id}
            |--------------------------------------------------------------------------
            */

            if ($method === 'DELETE') {

                $id = obtenerIdDesdeRuta($path);

                if ($id !== null) {

                    $service->eliminar($id);

                    return respuestaJson([
                        'success' => true,
                        'message' => 'Producto eliminado correctamente'
                    ]);
                }

                $id = obtenerIdPedidoDesdeRuta($path);

                if ($id !== null) {

                    $pedidoService->eliminar($id);

                    return respuestaJson([
                        'success' => true,
                        'message' => 'Pedido eliminado correctamente'
                    ]);
                }

                $id = obtenerIdDetalleDesdeRuta($path);

                if ($id !== null) {

                    $detalleService->eliminar($id);

                    return respuestaJson([
                        'success' => true,
                        'message' => 'Detalle del pedido eliminado correctamente'
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Ruta inexistente
            |--------------------------------------------------------------------------
            */

            return respuestaJson([
                'success' => false,
                'message' => 'Ruta no encontrada'
            ], HttpStatus::NOT_FOUND);


        } catch (Throwable $e) {

            return respuestaJson([
                'success' => false,
                'message' => $e->getMessage()
            ], HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }
);


/*
|--------------------------------------------------------------------------
| Servidor
|--------------------------------------------------------------------------
*/

$errorHandler = new DefaultErrorHandler();

$server = SocketHttpServer::createForDirectAccess(
    new NullLogger()
);

$server->expose('127.0.0.1:1337');

$server->start(
    $requestHandler,
    $errorHandler
);

trapSignal([SIGINT, SIGTERM]);

$server->stop();