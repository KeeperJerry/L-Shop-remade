<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users;

use app\DataTransferObjects\Admin\Users\ListResult;
use app\DataTransferObjects\PaginationList;
use app\Exceptions\InvalidArgumentException;
use app\Repository\User\UserRepository;
use app\Services\Auth\Activator;
use app\Services\Auth\BanManager;

class ListHandler
{
    private $availableOrders = ['id', 'username', 'email', 'balance'];

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var Activator
     */
    private $activator;

    /**
     * @var BanManager
     */
    private $banManager;

    public function __construct(UserRepository $repository, Activator $activator, BanManager $banManager)
    {
        $this->repository = $repository;
        $this->activator = $activator;
        $this->banManager = $banManager;
    }

    public function handle(PaginationList $dto): ListResult
    {
        if (!empty($dto->getOrderBy()) && !in_array($dto->getOrderBy(), $this->availableOrders)) {
            throw new InvalidArgumentException('`OrderBy` has illegal value');
        }

        if ($dto->getOrderBy() !== null) {
            if (!empty($dto->getSearch())) {
                $paginator = $this->repository->findPaginatedWithOrderAndSearch(
                    $dto->getOrderBy(),
                    $dto->isDescending(),
                    $dto->getSearch(),
                    $dto->getPage(),
                    $dto->getPerPage()
                );
            } else {
                $paginator = $this->repository->findPaginatedWithOrder(
                    $dto->getOrderBy(),
                    $dto->isDescending(),
                    $dto->getPage(),
                    $dto->getPerPage()
                );
            }
        } else {
            if (!empty($dto->getSearch())) {
                $paginator = $this->repository->findPaginateWithSearch(
                    $dto->getSearch(),
                    $dto->getPage(),
                    $dto->getPerPage()
                );
            } else {
                $paginator = $this->repository->findPaginated($dto->getPage(), $dto->getPerPage());
            }
        }

        return new ListResult($paginator, $this->activator, $this->banManager);
    }
}
