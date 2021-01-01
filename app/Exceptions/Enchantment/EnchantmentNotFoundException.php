<?php
declare(strict_types = 1);

namespace app\Exceptions\Enchantment;

use app\Exceptions\DomainException;

class EnchantmentNotFoundException extends DomainException
{
    public static function byId(int $id): EnchantmentNotFoundException
    {
        return new EnchantmentNotFoundException("Enchantment with id {$id} not found");
    }
}
