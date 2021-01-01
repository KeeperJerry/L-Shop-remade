<?php
declare(strict_types=1);

namespace app\Handlers\Admin\Items\Add;

use app\DataTransferObjects\Admin\Items\Add\Add;
use app\Entity\EnchantmentItem;
use app\Entity\Item;
use app\Events\Item\ItemCreatedEvent;
use app\Exceptions\Enchantment\EnchantmentNotFoundException;
use app\Exceptions\InvalidArgumentTypeException;
use app\Exceptions\UnexpectedValueException;
use app\Repository\Enchantment\EnchantmentRepository;
use app\Repository\Item\ItemRepository;
use app\Services\Item\Image\Hashing\Hasher;
use app\Services\Item\Image\Image;
use app\Services\Item\Type;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\UploadedFile;

class AddHandler
{
    private const IMAGE_DEFAULT = 'default';

    private const IMAGE_UPLOAD = 'upload';

    private const IMAGE_BROWSE = 'browse';

    /**
     * @var ItemRepository
     */
    private $repository;

    /**
     * @var EnchantmentRepository
     */
    private $enchantmentRepository;

    /**
     * @var Hasher
     */
    private $imageHasher;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(
        ItemRepository $repository,
        EnchantmentRepository $enchantmentRepository,
        Hasher $imageHasher,
        Dispatcher $eventDispatcher)
    {
        $this->repository = $repository;
        $this->enchantmentRepository = $enchantmentRepository;
        $this->imageHasher = $imageHasher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Add $dto
     *
     * @throws EnchantmentNotFoundException
     */
    public function handle(Add $dto): void
    {
        $image = $this->imageName($dto->getImageType(), $dto->getFile() ?: $dto->getImageName());

        $item = (new Item($dto->getName(), $dto->getItemType(), $dto->getSignature()))
            ->setDescription($dto->getDescription())
            ->setType($dto->getItemType())
            ->setImage($image)
            ->setExtra($dto->getExtra());

        if ($dto->getItemType() === Type::ITEM) {
            foreach ($dto->getEnchantments() as $each) {
                $enchantment = $this->enchantmentRepository->find($each->getId());
                if ($enchantment === null) {
                    throw EnchantmentNotFoundException::byId($each->getId());
                }

                $ei = new EnchantmentItem($enchantment, $each->getLevel());
                $ei->setItem($item);
                $item->getEnchantmentItems()->add($ei);
            }
        }

        $this->repository->create($item);
        $this->eventDispatcher->dispatch(new ItemCreatedEvent($item));
    }

    /**
     * @param string              $type
     * @param string|UploadedFile $fileOrName
     *
     * @return null|string Image name or null if image default.
     */
    private function imageName(string $type, $fileOrName): ?string
    {
        if ($type === self::IMAGE_DEFAULT) {
            return null;
        }

        if ($type === self::IMAGE_BROWSE) {
            return $fileOrName;
        }

        if ($type === self::IMAGE_UPLOAD) {
            if ($fileOrName instanceof UploadedFile) {
                return $this->moveAndGetName($fileOrName);
            }

            throw new InvalidArgumentTypeException('$fileOrName', UploadedFile::class, $fileOrName);
        }

        throw new UnexpectedValueException('Unexpected value (' . $type . ') of argument $type');
    }

    private function moveAndGetName(UploadedFile $file): string
    {
        $hash = $this->imageHasher->make($file->path());
        $filename = "{$hash}.{$file->getClientOriginalExtension()}";
        $file->move(Image::absolutePath(), $filename);

        return $filename;
    }
}
