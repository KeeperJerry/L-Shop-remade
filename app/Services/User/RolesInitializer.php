<?php
declare(strict_types = 1);

namespace app\Services\User;

use app\Entity\Role;
use app\Entity\User;
use app\Exceptions\Role\PermissionNotFoundException;
use app\Repository\Role\RoleRepository;
use app\Services\Database\GarbageCollection\DoctrineGarbageCollector;
use Illuminate\Contracts\Config\Repository;

class RolesInitializer
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var DoctrineGarbageCollector
     */
    private $gc;

    public function __construct(RoleRepository $roleRepository, Repository $config, DoctrineGarbageCollector $gc)
    {
        $this->config = $config;
        $this->roleRepository = $roleRepository;
        $this->gc = $gc;
    }

    public function attachDefaultRoles(User $user): void
    {
        $roles = $this->config->get('auth.role.default');
        foreach ($roles as $roleName) {
            /** @var Role $role */
            $role = $this->roleRepository->findByName($roleName);
            if ($role === null) {
                throw PermissionNotFoundException::byName($roleName);
            }

            $user->getRoles()->add($role);
            $role->getUsers()->add($user);

            $this->roleRepository->update($role);
        }
    }
}
