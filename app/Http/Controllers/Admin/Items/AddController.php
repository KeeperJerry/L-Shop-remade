<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Items;

use app\DataTransferObjects\Admin\Items\Add\Add;
use app\DataTransferObjects\Admin\Items\Add\EnchantmentFromFrontend;
use app\Handlers\Admin\Items\Add\AddHandler;
use app\Handlers\Admin\Items\Add\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Items\AddRequest;
use function app\permission_middleware;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notificator;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;

class AddController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_ITEMS_CRUD_ACCESS));
    }

    public function render(RenderHandler $handler)
    {
        $dto = $handler->handle();

        return new JsonResponse(Status::SUCCESS, [
            'images' => $dto->getImages(),
            'enchantments' => $dto->getEnchantments()
        ]);
    }

    public function add(AddRequest $request, AddHandler $handler, Notificator $notificator): JsonResponse
    {
        $enchantments = [];
        foreach (json_decode($request->get('enchantments'), true) as $item) {
            $enchantments[] = new EnchantmentFromFrontend($item['id'], $item['level']);
        }

        $dto = (new Add())
            ->setName($request->get('name'))
            ->setDescription($request->get('description'))
            ->setItemType($request->get('item_type'))
            ->setImageType($request->get('image_type'))
            ->setFile($request->file('file'))
            ->setImageName($request->get('image_name'))
            ->setSignature($request->get('signature'))
            ->setEnchantments($enchantments)
            ->setExtra($request->get('extra'));

        $handler->handle($dto);
        $notificator->notify(new Success(__('msg.admin.items.add.success')));

        return new JsonResponse(Status::SUCCESS);
    }
}
