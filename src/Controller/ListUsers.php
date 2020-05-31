<?php

namespace RestfulAPI\Controller;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;

final class ListUsers
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->db
            ->query('SELECT id, name FROM users ORDER BY id')
            ->then(function (QueryResult $queryResult) {
                $users = json_encode($queryResult->resultRows);
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    $users
                );
            });
    }
}
