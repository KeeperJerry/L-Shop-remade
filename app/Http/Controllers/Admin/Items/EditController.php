<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Items;

use app\DataTransferObjects\Admin\Items\Add\EnchantmentFromFrontend;
use app\DataTransferObjects\Admin\Items\Edit\Edit;
use app\Exceptions\Item\ItemNotFoundException;
use app\Handlers\Admin\Items\Edit\EditHandler;
use app\Handlers\Admin\Items\Edit\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Items\EditRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\permission_middleware;

class EditController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_ITEMS_CRUD_ACCESS));
    }

    public function render(Request $request, RenderHandler $handler): JsonResponse
    {
        try {
            return new JsonResponse(Status::SUCCESS, $handler->handle((int)$request->route('item')));
        } catch (ItemNotFoundException $e) {
            return (new JsonResponse('item_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND);
        }
    }

    public function edit(EditRequest $request, EditHandler $handler): JsonResponse
    {
        $enchantments = [];
        foreach (json_decode($request->get('enchantments'), true) as $item) {
            $enchantments[] = new EnchantmentFromFrontend($item['id'], $item['level']);
        }

        $dto = (new Edit())
            ->setId((int)$request->route('item'))
            ->setName($request->get('name'))
            ->setDescription($request->get('description'))
            ->setItemType($request->get('item_type'))
            ->setImageType($request->get('image_type'))
            ->setFile($request->file('file'))
            ->setImageName($request->get('image_name'))
            ->setSignature($request->get('signature'))
            ->setEnchantments($enchantments)
            ->setExtra($request->get('extra'));

        try {
            $handler->handle($dto);

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.items.edit.success')));
        } catch (ItemNotFoundException $e) {
            return (new JsonResponse('not_found'))
                ->addNotification(new Error(__('msg.admin.items.edit.not_found')));
        }
    }
}
