<?php

namespace RestfulAPI;

use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

final class Users
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function all(): PromiseInterface
    {
        return $this->db
            ->query('SELECT id, name, email FROM users ORDER BY id')
            ->then(function (QueryResult $queryResult) {
                return $queryResult->resultRows;
            });
    }

    public function create(string $name, string $email): PromiseInterface
    {
        return $this->db
            ->query(
                'INSERT INTO users(name, email) VALUES(?, ?)',
                [$name, $email]
            );
    }
}
