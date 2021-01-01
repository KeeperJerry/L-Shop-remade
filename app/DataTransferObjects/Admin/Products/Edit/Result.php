<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Products\Edit;

use app\DataTransferObjects\Admin\Products\Add\Item;
use app\DataTransferObjects\Admin\Products\Add\Server;

class Result
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var Item[]
     */
    private $items;

    /**
     * @var Server[]
     */
    private $servers;

    public function __construct(Product $product, array $items, array $servers)
    {
        $this->product = $product;
        $this->items = $items;
        $this->servers = $servers;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return Server[]
     */
    public function getServers(): array
    {
        return $this->servers;
    }
}
