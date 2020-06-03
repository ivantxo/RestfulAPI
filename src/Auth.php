<?php declare(strict_types=1);

namespace RestfulAPI;

use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;

final class Auth
{
    private $basicAuth;

    public function __construct(LoopInterface $loop, array $credentials)
    {
        $this->basicAuth = new PSR15Middleware($loop, \Middlewares\BasicAuthentication::class, [$credentials]);
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return call_user_func($this->basicAuth, $request, $next);
    }
}
