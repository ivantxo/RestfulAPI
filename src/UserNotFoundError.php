<?php

namespace RestfulAPI;

use RuntimeException;

final class UserNotFoundError extends RuntimeException
{
    public function __construct(string $message = "User not found")
    {
        parent::__construct($message);
    }
}
