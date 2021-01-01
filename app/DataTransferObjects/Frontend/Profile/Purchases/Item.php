<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Frontend\Profile\Purchases;

use app\Entity\PurchaseItem;
use app\Services\Item\Enchantment\Enchantment;
use app\Services\Item\Image\Image;
use app\Services\Product\Cost;
use app\Services\Product\Stack;

class Item implements \JsonSerializable
{
    /**
     * @var PurchaseItem
     */
    private $entity;

    public function __construct(PurchaseItem $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $product = $this->entity->getProduct();
        $item = $product->getItem();

        return [
            'name' => $item->getName(),
            'image' => Image::assetPathOrDefault($item->getImage()),
            'stack' => Stack::formatUnits($product),
            'amount' => $this->entity->getAmount() !== 0 ? $this->entity->getAmount() : 1,
            'cost' => Cost::calculate($product, $this->entity->getAmount()),
            'enchanted' => Enchantment::isEnchanted($item)
        ];
    }
}
