<?php
declare(strict_types=1);

namespace app\Handlers\Admin\Items\Edit;

use app\DataTransferObjects\Admin\Items\Edit\Enchantment;
use app\DataTransferObjects\Admin\Items\Edit\Item;
use app\DataTransferObjects\Admin\Items\Edit\Result;
use app\Entity\EnchantmentItem;
use app\Exceptions\Item\ItemNotFoundException;
use app\Repository\Enchantment\EnchantmentRepository;
use app\Repository\Item\ItemRepository;
use app\Services\Item\Image\Image;
use Illuminate\Filesystem\Filesystem;

class RenderHandler
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var EnchantmentRepository
     */
    private $enchantmentRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        ItemRepository $itemRepository,
        EnchantmentRepository $enchantmentRepository,
        Filesystem $filesystem)
    {
        $this->itemRepository = $itemRepository;
        $this->enchantmentRepository = $enchantmentRepository;
        $this->filesystem = $filesystem;
    }

    /**
     * @param int $itemId
     *
     * @return Result
     *
     * @throws ItemNotFoundException
     */
    public function handle(int $itemId): Result
    {
        $item = $this->itemRepository->find($itemId);
        if ($item === null) {
            throw ItemNotFoundException::byId($itemId);
        }
        $images = [];
        foreach ($this->filesystem->allFiles(Image::absolutePath()) as $image) {
            $images[] = new \app\DataTransferObjects\Admin\Items\Add\Image($image);
        }

        $enchantments = [];
        foreach ($this->enchantmentRepository->findAll() as $enchantment) {
            $ei = null;
            /** @var EnchantmentItem $enchantmentItem */
            foreach ($item->getEnchantmentItems() as $enchantmentItem) {
                if ($enchantmentItem->getEnchantment()->getId() === $enchantment->getId()) {
                    $ei = $enchantmentItem;
                    break;
                }
            }
            $enchantments[] = new Enchantment($enchantment, $ei);
        }

        return new Result(new Item($item), $images, $enchantments);
    }
}
