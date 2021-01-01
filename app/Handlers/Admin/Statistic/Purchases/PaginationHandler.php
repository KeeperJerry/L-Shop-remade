<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Statistic\Purchases;

use app\DataTransferObjects\Admin\Statistic\Purchases\PaginationResult;
use app\Exceptions\InvalidArgumentException;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\Auth\Auth;
use app\Services\Auth\Permissions;

class PaginationHandler
{
    /**
     * @var array
     */
    private $availableOrders = [
        'purchase.id',
        'purchase.cost',
        'purchase.player',
        'purchase.createdAt',
        'purchase.completedAt',
        'purchase.via'
    ];

    /**
     * @var PurchaseRepository
     */
    private $repository;

    /**
     * @var Auth
     */
    private $auth;

    public function __construct(PurchaseRepository $repository, Auth $auth)
    {
        $this->repository = $repository;
        $this->auth = $auth;
    }

    public function handle(int $page, int $perPage, ?string $orderBy, bool $descending): PaginationResult
    {
        if (!empty($orderBy) && !in_array($orderBy, $this->availableOrders)) {
            throw new InvalidArgumentException('Argument $orderBy has illegal value');
        }

        if ($orderBy !== null) {
            $paginator = $this->repository->findPaginatedWithOrder(
                $page,
                $orderBy,
                $descending,
                $perPage
            );
        } else {
            $paginator = $this->repository->findPaginated($page, $perPage);
        }

        return (new PaginationResult($paginator))
            ->setCanComplete($this->auth->getUser()->hasPermission(Permissions::ALLOW_COMPLETE_PURCHASES));
    }
}
