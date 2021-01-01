<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Permissions;

use app\Exceptions\Permission\PermissionAlreadyExistsException;
use app\Exceptions\Permission\PermissionNotFoundException;
use app\Repository\Permission\PermissionRepository;

class UpdateHandler
{
    /**
     * @var PermissionRepository
     */
    private $repository;

    public function __construct(PermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int    $permissionId
     * @param string $newName
     *
     * @throws PermissionNotFoundException
     * @throws PermissionAlreadyExistsException
     */
    public function handle(int $permissionId, string $newName): void
    {
        $permission = $this->repository->find($permissionId);

        if ($permission === null) {
            throw PermissionNotFoundException::byId($permissionId);
        }

        $permissionWithSameName = $this->repository->findByName($newName);
        if ($permissionWithSameName !== null && $permissionWithSameName->getId() !== $permission->getId()) {
            throw PermissionAlreadyExistsException::withName($newName);
        }

        $this->repository->update($permission->setName($newName));
    }
}
