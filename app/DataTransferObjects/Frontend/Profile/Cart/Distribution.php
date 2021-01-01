<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Frontend\Profile\Cart;

use app\Entity\Distribution as Entity;
use app\Services\Item\Image\Image;
use app\Services\Product\Stack;
use app\Services\Purchasing\Distributors\Attempting;

class Distribution implements \JsonSerializable
{
    /**
     * @var Entity
     */
    private $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $purchaseItem = $this->entity->getPurchaseItem();
        $distributorClass = $this
            ->entity
            ->getPurchaseItem()
            ->getProduct()
            ->getCategory()
            ->getServer()
            ->getDistributor();

        return [
            'id' => $this->entity->getId(),
            'amount' => Stack::formatUnitsForAmount($purchaseItem->getProduct(), $purchaseItem->getAmount()),
            'item' => [
                'name' => $purchaseItem->getProduct()->getItem()->getName(),
                'image' => Image::assetPathOrDefault($purchaseItem->getProduct()->getItem()->getImage())
            ],
            'product' => [
                'server' => $purchaseItem->getProduct()->getCategory()->getServer()->getName(),
                'category' => $purchaseItem->getProduct()->getCategory()->getName()
            ],
            'attempting' => in_array(Attempting::class, class_implements($distributorClass))
        ];
    }
}
