<?php
declare(strict_types = 1);

namespace app\Services\Game\Permissions\LuckPerms\Repository\Group;

use app\Services\Game\Permissions\LuckPerms\Entity\Group;

interface GroupRepository
{
    public function create(Group $group): void;

    public function update(Group $group): void;

    public function findByName(string $name): ?Group;

    public function deleteAll(): bool;
}
