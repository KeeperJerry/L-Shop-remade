<?php
declare(strict_types = 1);

namespace app\Exceptions\Purchase;

use app\Exceptions\DomainException;

class PurchaseNotFoundException extends DomainException
{
    public static function byId(int $id): PurchaseNotFoundException
    {
        return new PurchaseNotFoundException("Purchase with id {$id} not found");
    }
}
