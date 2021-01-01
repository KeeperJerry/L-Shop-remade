<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Edit;

use app\DataTransferObjects\Admin\Users\Edit\PaginationList;
use app\DataTransferObjects\Admin\Users\Edit\PurchasesResult;
use app\Exceptions\InvalidArgumentException;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\Auth\Auth;

class PurchasesHandler
{
    /**
     * @var array
     */
    private $availableOrders = [
        'purchase.id',
        'purchase.cost',
        'purchase.createdAt',
        'purchase.completedAt',
        'purchase.via'
    ];

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var PurchaseRepository
     */
    private $repository;

    public function __construct(Auth $auth, PurchaseRepository $repository)
    {
        $this->auth = $auth;
        $this->repository = $repository;
    }

    public function handle(PaginationList $dto)
    {
        if (!empty($dto->getOrderBy()) && !in_array($dto->getOrderBy(), $this->availableOrders)) {
            throw new InvalidArgumentException('Argument $orderBy has illegal value');
        }

        if ($dto->getOrderBy() !== null) {
            $paginator = $this->repository->findPaginatedWithOrderByUser(
                $this->auth->getUser(),
                $dto->getPage(),
                $dto->getOrderBy(),
                $dto->isDescending(),
                $dto->getPerPage()
            );
        } else {
            $paginator = $this->repository->findPaginatedByUser($this->auth->getUser(), $dto->getPage(), $dto->getPerPage());
        }

        return new PurchasesResult($paginator);
    }
}
