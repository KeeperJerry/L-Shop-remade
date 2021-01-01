<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Frontend\Shop\Catalog;

use app\Entity\EnchantmentItem;
use app\Services\Utils\NumberUtil;

class Enchantment implements \JsonSerializable
{
    /**
     * @var EnchantmentItem
     */
    private $enchantmentItem;

    /**
     * Enchantment constructor.
     *
     * @param EnchantmentItem $enchantmentItem
     */
    public function __construct(EnchantmentItem $enchantmentItem)
    {
        $this->enchantmentItem = $enchantmentItem;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => __("enchantments.names.{$this->enchantmentItem->getEnchantment()->getGameId()}"),
            'level' => NumberUtil::toRoman($this->enchantmentItem->getLevel())
        ];
    }
}
