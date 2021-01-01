<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Frontend\Shop;

use app\Entity\Server as Entity;

class Server implements \JsonSerializable
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
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->entity->getId(),
            'name' => $this->entity->getName()
        ];
    }
}
