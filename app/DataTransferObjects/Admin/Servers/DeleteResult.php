<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Servers;

use app\Services\Response\JsonRespondent;

class DeleteResult implements JsonRespondent
{
    /**
     * @var bool
     */
    private $destroyPersistence;

    public function __construct(bool $destroyPersistence)
    {
        $this->destroyPersistence = $destroyPersistence;
    }

    /**
     * @inheritDoc
     */
    public function response(): array
    {
        return [
            'destroyPersistence' => $this->destroyPersistence
        ];
    }
}
