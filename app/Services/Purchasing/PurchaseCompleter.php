<?php
declare(strict_types = 1);

namespace app\Services\Purchasing;

use app\Entity\Distribution;
use app\Entity\Purchase;
use app\Entity\PurchaseItem;
use app\Events\Purchase\PurchaseCompletedEvent;
use app\Exceptions\Distributor\DistributionException;
use app\Exceptions\Distributor\DistributorNotFoundException;
use app\Exceptions\Purchase\AlreadyCompletedException;
use app\Repository\Distribution\DistributionRepository;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\Purchasing\Distributors\Pool;
use app\Services\User\Balance\Transactor as BalanceTransactor;
use Illuminate\Contracts\Events\Dispatcher;

class PurchaseCompleter
{
    /**
     * @var PurchaseRepository
     */
    private $purchaseRepository;

    /**
     * @var Pool
     */
    private $distributors;

    /**
     * @var DistributionRepository
     */
    private $distributionRepository;

    /**
     * @var BalanceTransactor
     */
    private $balanceTransactor;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(
        PurchaseRepository $purchaseRepository,
        Pool $distributors,
        DistributionRepository $distributionRepository,
        BalanceTransactor $balanceTransactor,
        Dispatcher $eventDispatcher)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->distributors = $distributors;
        $this->distributionRepository = $distributionRepository;
        $this->balanceTransactor = $balanceTransactor;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Completes the purchase received and issues the products/shop currency to the player.
     *
     * @param Purchase    $purchase
     *
     * @param null|string $via Specifies the context in which the purchase was completed.
     *
     * @throws AlreadyCompletedException If the purchase has already been completed.
     * @throws DistributorNotFoundException
     * @throws DistributionException
     */
    public function complete(Purchase $purchase, ?string $via = null): void
    {
        if ($purchase->isCompleted()) {
            throw new AlreadyCompletedException($purchase);
        }

        $purchase->setCompletedAt(new \DateTimeImmutable());
        $purchase->setVia($via);
        $this->purchaseRepository->update($purchase);

        if (count($purchase->getItems()) !== 0) {
            // Retrieve name of distributor class.
            $distributorClass = $purchase
                ->getItems()
                ->first()
                ->getProduct()
                ->getCategory()
                ->getServer()
                ->getDistributor();

            // Retrieve distributor for server of this product.
            $distributor = $this->distributors->retrieveByName($distributorClass);

            if ($distributor === null) {
                throw DistributorNotFoundException::byClassName($distributorClass);
            }

            /** @var PurchaseItem $purchaseItem */
            foreach ($purchase->getItems() as $purchaseItem) {
                $distribution = new Distribution($purchaseItem);
                $this->distributionRepository->create($distribution);

                $distributor->distribute($distribution);
            }
        } else {
            // If this purchase is replenishment.
            $this->balanceTransactor->add($purchase->getUser(), $purchase->getCost());
        }

        $this->eventDispatcher->dispatch(new PurchaseCompletedEvent($purchase));
    }
}
