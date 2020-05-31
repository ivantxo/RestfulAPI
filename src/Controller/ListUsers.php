<?php

namespace RestfulAPI\Controller;

use RestfulAPI\Users;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

final class ListUsers
{
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->users->all()
            ->then(function (array $users) {
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    $users
                );
            });
    }
}
