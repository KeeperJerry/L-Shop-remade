<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Shop\Payment;

use app\Entity\Purchase;
use app\Exceptions\Payer\InvalidPaymentDataException;
use app\Exceptions\Payer\PayerNotFoundException;
use app\Exceptions\Purchase\AlreadyCompletedException;
use app\Exceptions\Purchase\PurchaseNotFoundException;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\Purchasing\Payers\Payer;
use app\Services\Purchasing\Payers\Pool;
use app\Services\Purchasing\PurchaseCompleter;
use Psr\Log\LoggerInterface;

class ResultHandler
{
    /**
     * @var Pool
     */
    private $payers;

    /**
     * @var PurchaseRepository
     */
    private $repository;

    /**
     * @var PurchaseCompleter
     */
    private $completer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Pool $payers,
        PurchaseRepository $repository,
        PurchaseCompleter $completer,
        LoggerInterface $logger)
    {
        $this->payers = $payers;
        $this->repository = $repository;
        $this->completer = $completer;
        $this->logger = $logger;
    }

    /**
     * @param string $payerName
     * @param array  $paymentData
     *
     * @return string
     * @throws PayerNotFoundException
     * @throws InvalidPaymentDataException
     * @throws AlreadyCompletedException
     */
    public function handle(string $payerName, array $paymentData): string
    {
        $payer = $this->findPayer($payerName);

        if (!$this->validate($payer, $paymentData)) {
            $e = new InvalidPaymentDataException($paymentData);
            $this->logger->warning($e);
            throw $e;
        }

        $purchase = $this->findPurchase($payer->purchaseId($paymentData));
        $this->completePurchase($purchase, $payer->name());

        return $payer->successAnswer($purchase);
    }

    /**
     * @param string $payerName
     *
     * @return Payer
     * @throws PayerNotFoundException
     */
    private function findPayer(string $payerName): Payer
    {
        $payer = $this->payers->retrieveByName($payerName);
        if ($payer === null) {
            $e = PayerNotFoundException::byName($payerName);
            $this->logger->warning($e);
            throw $e;
        }

        return $payer;
    }

    /**
     * @param Payer $payer
     * @param array $paymentData
     *
     * @return bool
     * @throws InvalidPaymentDataException
     */
    private function validate(Payer $payer, array $paymentData): bool
    {
        try {
            return $payer->validate($paymentData);
        } catch (\Exception $e) {
            $this->logger->warning($e);
            throw new InvalidPaymentDataException($paymentData);
        }
    }

    /**
     * @param int $id
     *
     * @return Purchase
     * @throws PurchaseNotFoundException
     */
    private function findPurchase(int $id): Purchase
    {
        $purchase = $this->repository->find($id);
        if ($purchase === null) {
            $e = PurchaseNotFoundException::byId($id);
            $this->logger->warning($e);
            throw $e;
        }

        return $purchase;
    }

    /**
     * @param Purchase $purchase
     * @param string   $via
     *
     * @throws AlreadyCompletedException
     */
    private function completePurchase(Purchase $purchase, string $via): void
    {
        try {
            $this->completer->complete($purchase, $via);
        } catch (AlreadyCompletedException $e) {
            $this->logger->warning($e);
            throw $e;
        }
    }
}
