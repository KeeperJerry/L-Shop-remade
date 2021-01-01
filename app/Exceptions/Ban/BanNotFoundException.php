<?php
declare(strict_types = 1);

namespace app\Exceptions\Ban;

use app\Exceptions\DomainException;

class BanNotFoundException extends DomainException
{
    public static function byId(int $id): BanNotFoundException
    {
        return new BanNotFoundException("Ban with id {$id} not found.");
    }
}
