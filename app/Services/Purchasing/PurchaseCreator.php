<?php
declare(strict_types=1);

namespace app\Services\Purchasing;

use app\DataTransferObjects\Frontend\Shop\Purchase as DTO;
use app\Entity\Purchase;
use app\Entity\PurchaseItem;
use app\Entity\User;
use app\Events\Purchase\PurchaseCreatedEvent;
use app\Exceptions\NotImplementedException;
use app\Exceptions\Purchase\InvalidAmountException;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\Item\Type;
use app\Services\Product\Stack;
use Illuminate\Contracts\Events\Dispatcher;

class PurchaseCreator
{
    /**
     * @var PurchaseRepository
     */
    private $purchaseRepository;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    /**
     * @var float
     */
    private $cost = 0;

    public function __construct(
        PurchaseRepository $purchaseRepository,
        Dispatcher $eventDispatcher)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param DTO[]       $dto
     * @param User|string $user
     * @param string      $ip
     *
     * @return Purchase
     * @throws \Exception
     */
    public function create(array $dto, $user, string $ip): Purchase
    {
        $this->through($dto);
        $purchase = $this->persist($dto, $user, $ip);
        $this->eventDispatcher->dispatch(new PurchaseCreatedEvent($purchase));

        return $purchase;
    }

    /**
     * Through all the elements and validates them and also increments the cost.
     *
     * @param DTO[] $dto
     */
    private function through(array $dto)
    {
        foreach ($dto as $each) {
            $product = $each->getProduct();
            $item = $product->getItem();

            switch ($item->getType()) {
                case Type::PERMGROUP:
                    if (Stack::isForever($product) === true) {
                        $this->addCost($product->getPrice());
                    } else {
                        $size = $this->validateAndCalculateAmount($each->getAmount(), $product->getStack());

                        if ($size === null) {
                            throw new InvalidAmountException($each->getAmount(), $product);
                        } else {
                            $this->addCost($product->getPrice(), $size);
                        }
                    }
                    break;
                case Type::ITEM:
                case Type::CURRENCY:
                    $size = $this->validateAndCalculateAmount($each->getAmount(), $product->getStack());

                    if ($size === null) {
                        throw new InvalidAmountException($each->getAmount(), $product);
                    } else {
                        $this->addCost($product->getPrice(), $size);
                    }
                    break;
                case Type::REGION_OWNER:
                case Type::REGION_MEMBER:
                case Type::COMMAND:
                    $this->addCost($product->getPrice(), 1);
                    break;
                default:
                    throw new NotImplementedException(
                        "Feature to handle this product type {$each->getProduct()} not implemented"
                    );
            }
        }
    }

    private function addCost(float $value, int $multiplier = 1): void
    {
        $this->cost += $value * $multiplier;
    }

    /**
     * @param int $amount
     * @param int $stack
     *
     * @return int|null Will return the number of stacks purchased in case of successful
     * verification and null - if the validation has failed.
     */
    private function validateAndCalculateAmount(int $amount, int $stack): ?int
    {
        if ($amount % $stack === 0) {
            return (int)floor($amount / $stack);
        }

        return null;
    }

    /**
     * @param DTO[]       $dto
     * @param User|string $user
     * @param string      $ip
     *
     * @return Purchase
     * @throws \Exception
     */
    private function persist(array $dto, $user, string $ip): Purchase
    {
        $purchase = new Purchase($this->cost, $ip);
        foreach ($dto as $each) {
            $purchaseItem = new PurchaseItem($each->getProduct(), $each->getAmount());
            $purchaseItem->setPurchase($purchase);
            $purchase->getItems()->add($purchaseItem);
        }
        if ($user instanceof User) {
            $purchase->setUser($user);
        } else {
            $purchase->setPlayer($user);
        }

        $this->purchaseRepository->create($purchase);

        return $purchase;
    }
}
