<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Permissions;

use app\Entity\Permission;
use app\Exceptions\Permission\PermissionAlreadyExistsException;
use app\Repository\Permission\PermissionRepository;

class CreateHandler
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
     * @param string $name
     *
     * @throws PermissionAlreadyExistsException
     */
    public function handle(string $name): void
    {
        $permission = $this->repository->findByName($name);

        if ($permission !== null) {
            throw PermissionAlreadyExistsException::withName($name);
        }

        $this->repository->create(new Permission($name));
    }
}
