<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Products\Add;

use app\DataTransferObjects\Admin\Products\Add\Add;
use app\Entity\Product;
use app\Events\Product\ProductCreatedEvent;
use app\Exceptions\Category\CategoryNotFoundException;
use app\Exceptions\Item\ItemNotFoundException;
use app\Repository\Category\CategoryRepository;
use app\Repository\Item\ItemRepository;
use app\Repository\Product\ProductRepository;
use Illuminate\Contracts\Events\Dispatcher;

class AddHandler
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(
        ProductRepository $repository,
        ItemRepository $itemRepository,
        CategoryRepository $categoryRepository,
        Dispatcher $eventDispatcher)
    {
        $this->productRepository = $repository;
        $this->itemRepository = $itemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Add $dto
     *
     * @throws ItemNotFoundException
     * @throws CategoryNotFoundException
     */
    public function handle(Add $dto): void
    {
        $item = $this->itemRepository->find($dto->getItem());
        if ($item === null) {
            throw ItemNotFoundException::byId($dto->getItem());
        }

        $category = $this->categoryRepository->find($dto->getCategory());
        if ($category === null) {
            throw CategoryNotFoundException::byId($dto->getCategory());
        }

        $product = (new Product($item, $category, $dto->getPrice(), $dto->getStack()))
            ->setSortPriority($dto->getSortPriority())
            ->setHidden($dto->isHidden());
        $this->productRepository->create($product);
        $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));
    }
}
