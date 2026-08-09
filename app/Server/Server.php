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
use App\Models\Producto;
use App\Repositories\ProductoRepository;
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


function obtenerIdDesdeRuta(string $path): ?int
{
    if (preg_match('#^/productos/(\d+)$#', $path, $matches)) {
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

    function (Request $request) use ($service, $repository): Response {

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