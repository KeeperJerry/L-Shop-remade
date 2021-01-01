<?php
declare(strict_types = 1);

namespace app\Exceptions\Permission;

use app\Exceptions\LogicException;

class PermissionAlreadyExistsException extends LogicException
{
    public static function withName(string $name)
    {
        return new PermissionAlreadyExistsException("Permission with name \"$name\" already exists");
    }
}
