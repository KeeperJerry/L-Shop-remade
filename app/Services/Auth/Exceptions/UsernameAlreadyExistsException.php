<?php
declare(strict_types = 1);

namespace app\Services\Auth\Exceptions;

class UsernameAlreadyExistsException extends AuthException
{
    public function __construct(string $username)
    {
        parent::__construct($username, 0, null);
    }
}
