<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Edit;

use app\DataTransferObjects\Admin\Users\Edit\CartResult;
use app\DataTransferObjects\Admin\Users\Edit\PaginationList;
use app\Exceptions\InvalidArgumentException;
use app\Repository\Distribution\DistributionRepository;
use app\Services\Auth\Auth;

class CartHandler
{
    /**
     * @var array
     */
    private $availableOrders = ['distribution.id', 'purchaseItem.amount', 'item.name', 'server.name'];

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var DistributionRepository
     */
    private $distributionRepository;

    public function __construct(Auth $auth, DistributionRepository $distributionRepository)
    {
        $this->distributionRepository = $distributionRepository;
        $this->auth = $auth;
    }

    /**
     * @param PaginationList $dto
     *
     * @return CartResult
     */
    public function handle(PaginationList $dto): CartResult
    {
        if (!empty($orderBy) && !in_array($orderBy, $this->availableOrders)) {
            throw new InvalidArgumentException('Argument $orderBy has illegal value');
        }

        if ($dto->getOrderBy() !== null) {
            $paginator = $this->distributionRepository->findbyUserPaginatedWithOrder(
                $this->auth->getUser(),
                $dto->getPage(),
                $dto->getOrderBy(),
                $dto->isDescending(),
                $dto->getPerPage()
            );
        } else {
            $paginator = $this->distributionRepository->findByUserPaginated(
                $this->auth->getUser(),
                $dto->getPage(),
                $dto->getPerPage()
            );
        }

        return new CartResult($paginator);
    }
}
