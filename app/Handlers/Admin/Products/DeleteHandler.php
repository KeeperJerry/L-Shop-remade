<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Products;

use app\Events\Product\ProductWillBeDeletedEvent;
use app\Exceptions\Product\ProductNotFoundException;
use app\Repository\Product\ProductRepository;
use Illuminate\Contracts\Events\Dispatcher;

class DeleteHandler
{
    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(ProductRepository $repository, Dispatcher $eventDispatcher)
    {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $productId
     *
     * @throws ProductNotFoundException
     */
    public function handle(int $productId): void
    {
        $product = $this->repository->find($productId);
        if ($product === null) {
            throw ProductNotFoundException::byId($productId);
        }

        $this->eventDispatcher->dispatch(new ProductWillBeDeletedEvent($product));
        $this->repository->remove($product);
    }
}
