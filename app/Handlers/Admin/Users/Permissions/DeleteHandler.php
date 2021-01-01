<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Permissions;

use app\Exceptions\Permission\PermissionNotFoundException;
use app\Repository\Permission\PermissionRepository;

class DeleteHandler
{
    /**
     * @var PermissionRepository
     */
    private $repository;

    public function __construct(PermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(int $permissionId): void
    {
        $permission = $this->repository->find($permissionId);

        if ($permission === null) {
            throw PermissionNotFoundException::byId($permissionId);
        }

        $this->repository->remove($permission);
    }
}
