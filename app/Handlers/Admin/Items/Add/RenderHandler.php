<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Items\Add;

use app\DataTransferObjects\Admin\Items\Add\Enchantment;
use app\DataTransferObjects\Admin\Items\Add\Image;
use app\DataTransferObjects\Admin\Items\Add\Result;
use app\Repository\Enchantment\EnchantmentRepository;
use Illuminate\Filesystem\Filesystem;

class RenderHandler
{
    /**
     * @var EnchantmentRepository
     */
    private $enchantmentRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(EnchantmentRepository $enchantmentRepository, Filesystem $filesystem)
    {
        $this->enchantmentRepository = $enchantmentRepository;
        $this->filesystem = $filesystem;
    }

    public function handle(): Result
    {
        $images = [];
        foreach ($this->filesystem->allFiles(\app\Services\Item\Image\Image::absolutePath()) as $item) {
            $images[] = new Image($item);
        }

        $enchantments = [];
        foreach ($this->enchantmentRepository->findAll() as $each) {
            $enchantments[] = new Enchantment($each);
        }

        return new Result($images, $enchantments);
    }
}
