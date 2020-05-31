<?php

namespace RestfulAPI;

use React\Http\Response;

final class JsonResponse extends Response
{
    public function __construct(int $statusCode, $data = null)
    {
        $body = $data ? json_encode($data) : null;
        parent::__construct(
            $statusCode,
            ['Content-Type' => 'application/json'],
            $body
        );
    }

    public static function ok($data = null): self
    {
        return new self(200, $data);
    }
}
