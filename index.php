<?php

use RestfulAPI\Auth;
use RestfulAPI\Controller\ListUsers;
use RestfulAPI\Controller\CreateUser;
use RestfulAPI\Controller\ViewUser;
use RestfulAPI\Controller\UpdateUser;
use RestfulAPI\Controller\DeleteUser;
use RestfulAPI\Router;
use RestfulAPI\Users;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use React\Http\Server;
use React\MySQL\Factory;

require './vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:mysql@localhost/restful');
$users = new Users($db);

$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/users', new ListUsers($users));
$routes->post('/users', new CreateUser($users));
$routes->get('/users/{id}', new ViewUser($users));
$routes->put('/users/{id}', new UpdateUser($users));
$routes->delete('/users/{id}', new DeleteUser($users));

$server = new Server([
    new Auth($loop, ['user' => 'secret']),
    new Router($routes),
]);
$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

$server->on('error', function (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
});

echo 'Listening on '
    . str_replace('tcp:', 'http:', $socket->getAddress())
    . PHP_EOL;

$loop->run();
