<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Users\Permissions;

use app\Entity\Permission as Entity;

class Permission implements \JsonSerializable
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
        return [
            'id' => $this->entity->getId(),
            'name' => $this->entity->getName()
        ];
    }
}
