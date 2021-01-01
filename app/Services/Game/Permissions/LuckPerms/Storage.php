<?php
declare(strict_types = 1);

namespace app\Services\Game\Permissions\LuckPerms;

use app\Entity\User;
use app\Repository\Server\ServerRepository;
use app\Services\Game\Permissions\Group;
use app\Services\Game\Permissions\LuckPerms\Entity\GroupPermission;
use app\Services\Game\Permissions\LuckPerms\Entity\Permission as PermissionEntity;
use app\Services\Game\Permissions\LuckPerms\Entity\Player as PlayerEntity;
use app\Services\Game\Permissions\LuckPerms\Entity\PlayerPermission;
use app\Services\Game\Permissions\LuckPerms\Repository\Group\GroupRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\GroupPermission\GroupPermissionRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\Player\PlayerRepository;
use app\Services\Game\Permissions\Permission;
use app\Services\Game\Permissions\Player;
use app\Services\Game\Permissions\PlayerWithUser;
use app\Services\Game\Permissions\PlayerWithUsername;
use app\Services\Game\Permissions\PlayerWithUuid;
use app\Services\Game\Permissions\Storage as StorageInterface;
use Ramsey\Uuid\UuidInterface;

class Storage implements StorageInterface
{
    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var GroupPermissionRepository
     */
    private $groupPermissionRepository;

    /**
     * @var ServerRepository
     */
    private $serverRepository;

    public function __construct(
        PlayerRepository $playerRepository,
        GroupRepository $groupRepository,
        ServerRepository $serverRepository,
        GroupPermissionRepository $groupPermissionRepository)
    {
        $this->playerRepository = $playerRepository;
        $this->groupRepository = $groupRepository;
        $this->groupPermissionRepository = $groupPermissionRepository;
        $this->serverRepository = $serverRepository;
    }

    public function retrievePlayerByUser(User $user): ?Player
    {
        $playerFromStorage = $this->playerRepository->findByUsername($user->getUsername());
        if ($playerFromStorage !== null) {
            $primaryGroup = $this->retrieveGroup($playerFromStorage->getPrimaryGroup()->getName());
            $player = new PlayerWithUser($user, $primaryGroup);

            return $this->fillPlayer($playerFromStorage, $player);
        }

        return null;
    }

    public function retrievePlayerByUsername(string $username): ?Player
    {
        $playerFromStorage = $this->playerRepository->findByUsername($username);
        if ($playerFromStorage !== null) {
            $primaryGroup = $this->retrieveGroup($playerFromStorage->getPrimaryGroup()->getName());
            $player = new PlayerWithUsername($username, $primaryGroup);

            return $this->fillPlayer($playerFromStorage, $player);
        }

        return null;
    }

    public function retrievePlayerByUuid(UuidInterface $uuid): ?Player
    {
        $playerFromStorage = $this->playerRepository->findByUuid($uuid);
        if ($playerFromStorage !== null) {
            $primaryGroup = $this->retrieveGroup($playerFromStorage->getPrimaryGroup()->getName());
            $player = new PlayerWithUuid($uuid, $primaryGroup);

            return $this->fillPlayer($playerFromStorage, $player);
        }

        return null;
    }

    private function fillPlayer(PlayerEntity $playerFromStorage, Player $player): Player
    {
        /** @var PlayerPermission $permission */
        foreach ($playerFromStorage->getPermissions() as $permission) {
            $newPermission = $this->fillPermission($permission);
            if ($this->expired($newPermission->getExpireAt())) {
                // Remove permission if it has expired.
                $playerFromStorage->getPermissions()->removeElement($permission);
            } else {
                $player->getPermissions()->add($newPermission);

                $parentGroupName = $this->getGroupNameByPermission($newPermission);
                if ($parentGroupName !== null) {
                    $parentGroup = $this->retrieveGroup($parentGroupName);
                    if ($parentGroup !== null) {
                        $player->getGroups()->add($parentGroup);
                    }
                }
            }
        }
        // Updates the player. If he has removed the permissions, the changes will
        // take effect in the repository.
        $this->playerRepository->update($playerFromStorage);

        return $player;
    }

    public function retrieveGroup(string $name): ?Group
    {
        $groupFromStorage = $this->groupRepository->findByName($name);
        if ($groupFromStorage !== null) {
            $group = new Group($groupFromStorage->getName());

            /** @var GroupPermission $permission */
            foreach ($groupFromStorage->getPermissions() as $permission) {
                $newPermission = $this->fillPermission($permission);
                if ($this->expired($newPermission->getExpireAt())) {
                    // Remove permission if it has expired.
                    $groupFromStorage->getPermissions()->removeElement($permission);
                } else {
                    $group->getPermissions()->add($newPermission);

                    $parentGroupName = $this->getGroupNameByPermission($newPermission);
                    if ($parentGroupName !== null) {
                        $parentGroup = $this->retrieveGroup($parentGroupName);
                        $group->setExpireAt($newPermission->getExpireAt());
                        $group->getParents()->add($parentGroup);
                    }
                }
            }
            // Updates the group. If he has removed the permissions, the changes will
            // take effect in the repository.
            $this->groupRepository->update($groupFromStorage);

            return $group;
        }

        return null;
    }

    /**
     * @param PermissionEntity $from
     *
     * @return Permission
     */
    private function fillPermission(PermissionEntity $from): Permission
    {
        return (new Permission($from->getPermission()))->setAllowed($from->getValue())
            ->setServer($from->getServer())
            ->setWorld($from->getWorld())
            ->setExpireAt(
                $from->getExpireAt() !== 0
                    ? \DateTimeImmutable::createFromFormat('U', (string)$from->getExpireAt())
                    : null
            )
            ->setContexts($from->getContext());
    }

    private function getGroupNameByPermission(Permission $permission): ?string
    {
        if (mb_strpos($permission->getName(), 'group') === 0) {
            return mb_substr($permission->getName(), mb_strlen('group.'));
        }

        return null;
    }

    /**
     * @param \DateTimeImmutable|null $expireAt
     *
     * @return bool
     * @throws \Exception
     */
    private function expired(?\DateTimeImmutable $expireAt): bool
    {
        if ($expireAt === null) {
            return false;
        }

        return (new \DateTimeImmutable())
                ->diff($expireAt)
                ->invert !== 0;
    }
}
