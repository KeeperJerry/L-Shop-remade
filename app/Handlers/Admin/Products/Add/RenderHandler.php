<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Products\Add;

use app\DataTransferObjects\Admin\Products\Add\Item;
use app\DataTransferObjects\Admin\Products\Add\Result;
use app\DataTransferObjects\Admin\Products\Add\Server;
use app\Repository\Item\ItemRepository;
use app\Repository\Server\ServerRepository;

class RenderHandler
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;
    /**
     * @var ServerRepository
     */
    private $serverRepository;

    public function __construct(ItemRepository $itemRepository, ServerRepository $serverRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->serverRepository = $serverRepository;
    }

    public function handle(): Result
    {
        $items = [];
        foreach ($this->itemRepository->findAll() as $item) {
            $items[] = new Item($item);
        }

        $servers = [];
        foreach ($this->serverRepository->findAll() as $server) {
            $servers[] = new Server($server);
        }

        return new Result($items, $servers);
    }
}
