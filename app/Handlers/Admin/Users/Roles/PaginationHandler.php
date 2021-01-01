<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Roles;

use app\DataTransferObjects\Admin\Users\Roles\ListResult;
use app\DataTransferObjects\PaginationList;
use app\Exceptions\InvalidArgumentException;
use app\Repository\Permission\PermissionRepository;
use app\Repository\Role\RoleRepository;

class PaginationHandler
{
    private $availableOrders = ['role.id', 'role.name'];

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

    public function handle(PaginationList $dto)
    {
        if (!empty($dto->getOrderBy()) && !in_array($dto->getOrderBy(), $this->availableOrders)) {
            throw new InvalidArgumentException('`OrderBy` has illegal value');
        }

        if ($dto->getOrderBy() !== null) {
            if (!empty($dto->getSearch())) {
                $paginator = $this->roleRepository->findPaginatedWithOrderAndSearch(
                    $dto->getOrderBy(),
                    $dto->isDescending(),
                    $dto->getSearch(),
                    $dto->getPage(),
                    $dto->getPerPage()
                );
            } else {
                $paginator = $this->roleRepository->findPaginatedWithOrder(
                    $dto->getOrderBy(),
                    $dto->isDescending(),
                    $dto->getPage(),
                    $dto->getPerPage()
                );
            }
        } else {
            if (!empty($search)) {
                $paginator = $this->roleRepository->findPaginateWithSearch(
                    $dto->getSearch(),
                    $dto->getPage(),
                    $dto->getPerPage()
                );
            } else {
                $paginator = $this->roleRepository->findPaginated($dto->getPage(), $dto->getPerPage());
            }
        }

        $permissions = $this->permissionRepository->findAll();

        return new ListResult($paginator, $permissions);
    }
}
