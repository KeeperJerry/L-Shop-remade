<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Servers\Add;

use app\Services\Response\JsonRespondent;

class RenderResult implements JsonRespondent
{
    /**
     * Names of distributor classes.
     *
     * @var string[]
     */
    private $distributors;

    public function __construct(array $distributors)
    {
        $this->distributors = $distributors;
    }

    /**
     * @inheritDoc
     */
    public function response(): array
    {
        return [
            'distributors' => $this->distributors
        ];
    }
}
