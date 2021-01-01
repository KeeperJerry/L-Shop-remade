<?php
declare(strict_types = 1);

namespace app\Services\Game\Permissions\LuckPerms\Repository\GroupPermission;

use app\Services\Game\Permissions\LuckPerms\Entity\GroupPermission;

interface GroupPermissionRepository
{
    /**
     * @param string $permission
     *
     * @return GroupPermission[]
     */
    public function findByPermission(string $permission): array;

    public function findAll(): array;

    public function deleteAll(): bool;
}
