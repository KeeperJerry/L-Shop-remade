<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Servers\Edit;

use app\Services\Response\JsonRespondent;

class RenderResult implements JsonRespondent
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var array
     */
    private $distributors;

    public function __construct(Server $server, array $distributors)
    {
        $this->server = $server;
        $this->distributors = $distributors;
    }

    /**
     * @inheritDoc
     */
    public function response(): array
    {
        return [
            'server' => $this->server,
            'distributors' => $this->distributors
        ];
    }
}
