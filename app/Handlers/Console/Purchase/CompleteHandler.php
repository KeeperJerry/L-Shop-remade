<?php
declare(strict_types = 1);

namespace app\Handlers\Console\Purchase;

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
        $this->repository = $repository;
        $this->completer = $completer;
    }

    /**
     * @param int $purchaseId
     *
     * @throws PurchaseNotFoundException
     */
    public function handle(int $purchaseId): void
    {
        $purchase = $this->repository->find($purchaseId);
        if ($purchase === null) {
            throw PurchaseNotFoundException::byId($purchaseId);
        }

        $this->completer->complete($purchase, ViaContext::BY_ADMIN);
    }
}
