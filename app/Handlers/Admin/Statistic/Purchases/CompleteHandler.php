<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Statistic\Purchases;

use app\Entity\Purchase;
use app\Exceptions\Distributor\DistributionException;
use app\Exceptions\Purchase\AlreadyCompletedException;
use app\Exceptions\Purchase\PurchaseNotFoundException;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\Purchasing\PurchaseCompleter;
use app\Services\Purchasing\ViaContext;

class CompleteHandler
{
    /**
     * @var PurchaseRepository
     */
    private $repository;

    /**
     * @var PurchaseCompleter
     */
    private $completer;

    public function __construct(PurchaseRepository $repository, PurchaseCompleter $completer)
    {
        $this->completer = $completer;
        $this->repository = $repository;
    }

    /**
     * @param int $purchaseId The purchase identifier to be completed.
     *
     * @return Purchase
     *
     * @throws PurchaseNotFoundException If the purchase with the received identifier is not found.
     * @throws AlreadyCompletedException If this purchase is already completed.
     * @throws DistributionException If an error occurred while issuing products to the player.
     */
    public function handle(int $purchaseId): Purchase
    {
        $purchase = $this->repository->find($purchaseId);
        if ($purchase === null) {
            throw PurchaseNotFoundException::byId($purchaseId);
        }

        $this->completer->complete($purchase, ViaContext::BY_ADMIN);

        return $purchase;
    }
}
