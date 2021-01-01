<?php
declare(strict_types = 1);

namespace app\Events\Item;

use app\Entity\Item;

class ItemCreatedEvent
{
    /**
     * @var Item
     */
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }
}
