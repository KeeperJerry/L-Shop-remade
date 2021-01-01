<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Roles;

use app\Exceptions\Role\RoleNotFoundException;
use app\Repository\Role\RoleRepository;

class DeleteHandler
{
    /**
     * @var RoleRepository
     */
    private $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(int $roleId): void
    {
        $role = $this->repository->find($roleId);

        if ($role === null) {
            throw RoleNotFoundException::byId($roleId);
        }

        $this->repository->remove($role);
    }
}
