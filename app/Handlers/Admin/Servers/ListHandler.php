<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Servers;

use app\DataTransferObjects\Admin\Servers\EditList\Server;
use app\Repository\Server\ServerRepository;

class ListHandler
{
    /**
     * @var ServerRepository
     */
    private $repository;

    public function __construct(ServerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(): array
    {
        $servers = $this->repository->findAllWithCategories();

        $result = [];
        foreach ($servers as $server) {
            $result[] = new Server($server);
        }

        return $result;
    }
}
