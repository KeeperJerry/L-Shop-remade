<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Frontend\Profile\Purchases;

use app\Entity\Purchase as Entity;
use app\Services\DateTime\Formatting\JavaScriptFormatter;
use app\Services\Purchasing\ViaContext;

class Purchase implements \JsonSerializable
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
        $items = [];
        foreach ($this->entity->getItems() as $item) {
            $items[] = new Item($item);
        }

        return [
            'id' => $this->entity->getId(),
            'cost' => $this->entity->getCost(),
            'createdAt' => (new JavaScriptFormatter())->format($this->entity->getCreatedAt()),
            'completedAt' => $this->entity->getCompletedAt() !== null ?
                (new JavaScriptFormatter())->format($this->entity->getCompletedAt()) : null,
            'via' => [
                'quick' => $this->entity->getVia() === ViaContext::QUICK,
                'byAdmin' => $this->entity->getVia() === ViaContext::BY_ADMIN,
                'value' => $this->entity->getVia()
            ],
            'items' => $items
        ];
    }
}
