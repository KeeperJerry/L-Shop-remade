<?php
declare(strict_types = 1);

namespace app\Services\Auth\Acl;

interface RoleInterface extends HasPermissions
{
    public function getName(): string;
}
