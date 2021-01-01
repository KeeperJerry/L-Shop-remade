<?php
declare(strict_types=1);

namespace app\Http\Controllers\Admin\Products;

use app\DataTransferObjects\Admin\Products\Add\Add;
use app\Exceptions\Category\CategoryNotFoundException;
use app\Exceptions\Item\ItemNotFoundException;
use app\Handlers\Admin\Products\Add\AddHandler;
use app\Handlers\Admin\Products\Add\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Products\AddEditRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Response;
use function app\permission_middleware;

class AddController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_PRODUCTS_CRUD_ACCESS));
    }

    public function render(RenderHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function add(AddEditRequest $request, AddHandler $handler): JsonResponse
    {
        $dto = (new Add())
            ->setItem((int)$request->get('item'))
            ->setCategory((int)$request->get('category'))
            ->setPrice((float)$request->get('price'))
            ->setStack($request->get('forever') ? 0 : (int)$request->get('stack'))
            ->setSortPriority((float)$request->get('sort_priority'))
            ->setHidden((bool)$request->get('hidden'));

        try {
            $handler->handle($dto);

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.products.add.success')));
        } catch (ItemNotFoundException $e) {
            return (new JsonResponse('item_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Success(__('msg.admin.products.add.item_not_found')));
        } catch (CategoryNotFoundException $e) {
            return (new JsonResponse('category_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Success(__('msg.admin.products.add.category_not_found')));
        }
    }
}
