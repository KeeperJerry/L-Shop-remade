<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Products\Add;

use app\Entity\Category as Entity;

class Category implements \JsonSerializable
{
    /**
     * @var Entity
     */
    private $category;

    public function __construct(Entity $category)
    {
        $this->category = $category;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->category->getId(),
            'name' => $this->category->getName()
        ];
    }
}
