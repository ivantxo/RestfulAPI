<?php

use React\Http\Response;
use React\Http\Server;
use React\MySQL\Factory;
use Psr\Http\Message\ServerRequestInterface;
use \RestfulAPI\Controller\ListUsers;
use \RestfulAPI\Controller\CreateUser;
use \RestfulAPI\Users;

require './vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:mysql@localhost/restful');

$users = new Users($db);

$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $routes) use ($users) {
        $routes->addRoute('GET', '/users', new ListUsers($users));
        $routes->addRoute('POST', '/users', new CreateUser($users));
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
