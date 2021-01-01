<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Products\EditList;

use app\Entity\Product as Entity;
use app\Services\Item\Image\Image;
use app\Services\Product\Stack;

class Product implements \JsonSerializable
{
    /**
     * @var Entity
     */
    private $product;

    public function __construct(Entity $product)
    {
        $this->product = $product;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->product->getId(),
            'price' => $this->product->getPrice(),
            'stack' => Stack::formatUnits($this->product),
            'server' => [
                'name' => $this->product->getCategory()->getServer()->getName()
            ],
            'category' => [
                'name' => $this->product->getCategory()->getName()
            ],
            'item' => [
                'name' => $this->product->getItem()->getName(),
                'image' => Image::assetPathOrDefault($this->product->getItem()->getImage()),
                'type' => __("common.item.type.{$this->product->getItem()->getType()}"),
                'enchanted' => $this->product->getItem()->getEnchantmentItems()->count() !== 0
            ],
            'hidden' => $this->product->isHidden()
        ];
    }
}
