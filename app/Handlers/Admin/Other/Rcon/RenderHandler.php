<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Other\Rcon;

use app\DataTransferObjects\Admin\Other\Rcon\Server;
use app\Repository\Server\ServerRepository;

class RenderHandler
{
    /**
     * @var ServerRepository
     */
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    public function handle(): array
    {
        $servers = [];
        foreach ($this->serverRepository->findAll() as $server) {
            $servers[] = new Server($server);
        }

        return $servers;
    }
}
