<?php
declare(strict_types=1);

namespace app\Handlers\Frontend\Profile\Purchases;

use app\DataTransferObjects\Frontend\Profile\Purchases\ListResult;
use app\Exceptions\InvalidArgumentException;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\Auth\Auth;
use app\Services\Auth\Permissions;

class PaginationHandler
{
    private const PER_PAGE = 25;

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

    public function handle(int $page, ?string $orderBy, bool $descending): ListResult
    {
        if (!empty($orderBy) && !in_array($orderBy, $this->availableOrders)) {
            throw new InvalidArgumentException('Argument $orderBy has illegal value');
        }

        if ($orderBy !== null) {
            $paginator = $this->repository->findPaginatedWithOrderByUser(
                $this->auth->getUser(),
                $page,
                $orderBy,
                $descending,
                self::PER_PAGE
            );
        } else {
            $paginator = $this->repository->findPaginatedByUser(
                $this->auth->getUser(),
                $page,
                self::PER_PAGE
            );
        }

        return (new ListResult($paginator))
            ->setCanComplete($this->auth->getUser()->hasPermission(Permissions::ALLOW_COMPLETE_PURCHASES));
    }
}
