<?php

require __DIR__ . '/../../vendor/autoload.php';

use Amp\Http\HttpStatus;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\ClosureRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Psr\Log\NullLogger;

use function Amp\trapSignal;

$requestHandler = new ClosureRequestHandler(
    function (Request $request): Response {

    if ($request->getMethod() === 'OPTIONS') {
    return new Response(
        status: HttpStatus::NO_CONTENT,
        headers: [
            'Access-Control-Allow-Origin' => 'http://127.0.0.1:5500',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
        ]
    );
}

        return new Response(
            status: HttpStatus::OK,
            headers: [
                headers: [
    'Content-Type' => 'application/json',
    'Access-Control-Allow-Origin' => 'http://127.0.0.1:5500',
    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
    'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
       ],
            ],
            body: 'Hola desde el proyecto de ecommerce!'
        );
    }
);

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