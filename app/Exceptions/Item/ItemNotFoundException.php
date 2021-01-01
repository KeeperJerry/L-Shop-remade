<?php
declare(strict_types = 1);

namespace app\Exceptions\Item;

use app\Exceptions\DomainException;

class ItemNotFoundException extends DomainException
{
    public static function byId(int $id): ItemNotFoundException
    {
        return new ItemNotFoundException("Item with id {$id} not found");
    }
}
