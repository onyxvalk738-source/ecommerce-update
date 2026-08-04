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

        return new Response(
            status: HttpStatus::OK,
            headers: [
                'content-type' => 'text/plain'
            ],
            body: 'Hola desde Amp!'
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