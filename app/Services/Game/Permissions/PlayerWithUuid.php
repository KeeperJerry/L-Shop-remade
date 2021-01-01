<?php
declare(strict_types = 1);

namespace app\Services\Game\Permissions;

use Ramsey\Uuid\UuidInterface;

class PlayerWithUuid extends Player
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    public function __construct(UuidInterface $uuid, Group $primaryGroup)
    {
        parent::__construct($primaryGroup);
        $this->uuid = $uuid;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
