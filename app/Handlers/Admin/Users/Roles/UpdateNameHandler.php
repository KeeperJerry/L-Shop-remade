<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Roles;

use app\Exceptions\Role\RoleAlreadyExistsException;
use app\Exceptions\Role\RoleNotFoundException;
use app\Repository\Role\RoleRepository;

class UpdateNameHandler
{
    /**
     * @var RoleRepository
     */
    private $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int    $roleId
     * @param string $newName
     *
     * @throws RoleAlreadyExistsException
     */
    public function handle(int $roleId, string $newName): void
    {
        $role = $this->repository->find($roleId);

        if ($role === null) {
            throw RoleNotFoundException::byId($roleId);
        }

        $roleWithSameName = $this->repository->findByName($newName);
        if ($roleWithSameName !== null && $roleWithSameName->getId() !== $role->getId()) {
            throw RoleAlreadyExistsException::withName($newName);
        }

        $this->repository->update($role->setName($newName));
    }
}
