<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Roles;

use app\Entity\Role;
use app\Exceptions\Permission\PermissionNotFoundException;
use app\Exceptions\Role\RoleAlreadyExistsException;
use app\Repository\Permission\PermissionRepository;
use app\Repository\Role\RoleRepository;

class CreateHandler
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param string $name
     * @param array $permissions
     *
     * @throws PermissionNotFoundException
     * @throws RoleAlreadyExistsException
     */
    public function handle(string $name, array $permissions): void
    {
        $allPermissions = $this->permissionRepository->findAll();
        $role = new Role($name);

        foreach ($permissions as $permission) {
            $permissionEntity = null;
            foreach ($allPermissions as $each) {
                if ($permission === $each->getName()) {
                    $permissionEntity = $each;
                }
            }

            if ($permissionEntity === null) {
                throw PermissionNotFoundException::byName($permission);
            } else {
                $role->getPermissions()->add($permissionEntity);
            }
        }

        if ($this->roleRepository->findByName($name) !== null) {
            throw RoleAlreadyExistsException::withName($name);
        }

        $this->roleRepository->create($role);
    }
}
