<?php
declare(strict_types = 1);

namespace app\Exceptions\Server;

use app\Exceptions\DomainException;

class ServerNotFoundException extends DomainException
{
    public static function byId(int $id): ServerNotFoundException
    {
        return new ServerNotFoundException("Server with id {$id} not found");
    }
}
