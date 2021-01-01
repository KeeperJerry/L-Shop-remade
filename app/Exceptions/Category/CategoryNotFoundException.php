<?php
declare(strict_types = 1);

namespace app\Exceptions\Category;

use app\Exceptions\DomainException;

class CategoryNotFoundException extends DomainException
{
    public static function byId(int $id): CategoryNotFoundException
    {
        return new CategoryNotFoundException("Category with id {$id} not found");
    }
}
