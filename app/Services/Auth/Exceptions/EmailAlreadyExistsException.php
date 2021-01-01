<?php
declare(strict_types = 1);

namespace app\Services\Auth\Exceptions;

class EmailAlreadyExistsException extends AuthException
{
    public function __construct(string $email)
    {
        parent::__construct($email, 0, null);
    }
}
