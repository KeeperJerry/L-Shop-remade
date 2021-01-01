<?php
declare(strict_types = 1);

namespace app\Services\Auth\Acl;

interface PermissionInterface
{
    public function getName(): string;
}
