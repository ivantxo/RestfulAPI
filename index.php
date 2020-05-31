<?php

use React\Http\Response;
use React\Http\Server;
use React\MySQL\Factory;
use Psr\Http\Message\ServerRequestInterface;

require './vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:mysql@localhost/restful');

$hello = function () {
    return new Response(
        200,
        ['Content-Type' => 'text/plain'],
        'Hello' . PHP_EOL
    );
};

$listUsers = function () use ($db) {
    return $db
        ->query('
            SELECT id, name, email
            FROM users
            ORDER BY id
        ')
        ->then(function (\React\MySQL\QueryResult $queryResult) {
            $users = json_encode($queryResult->resultRows);
            return new Response(
                200,
                ['Content-Type' => 'application/json'],
                $users
            );
        });
};

$createUser = function (ServerRequestInterface $request) use ($db) {
    $user = json_decode((string) $request->getBody(), true);
    return $db
        ->query('INSERT INTO users(name, email) VALUES (?, ?)', $user)
        ->then(
            function () {
                return new Response(201);
            },
            function (Exception $error) {
                return new Response(
                    400,
                    ['Content-Type' => 'application/json'],
                    json_encode(['error' => $error->getMessage()])
                );
            }
        );
};

$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $routes) use ($listUsers, $createUser) {
        $routes->addRoute('GET', '/users', $listUsers);
        $routes->addRoute('POST', '/users', $createUser);
    }
);

$server = new Server(
    function (ServerRequestInterface $request) use ($dispatcher) {
        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(), $request->getUri()->getPath()
        );
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                return new Response(
                    404,
                    ['Content-Type' => 'text/plain'],
                    'Not found' . PHP_EOL
                );

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(
                    405,
                    ['Content-Type' => 'text/plain'],
                    'Method not allowed' . PHP_EOL
                );

            case FastRoute\Dispatcher::FOUND:
                return $routeInfo[1]($request);
        }
        throw new LogicException('Something went wrong in routing.');
    }
);
$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on '
    . str_replace('tcp:', 'http:', $socket->getAddress())
    . PHP_EOL;

$loop->run();
