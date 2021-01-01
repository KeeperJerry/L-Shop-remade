<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Pages;

use app\DataTransferObjects\PaginationList;
use app\Exceptions\Page\PageNotFoundException;
use app\Handlers\Admin\Pages\DeleteHandler;
use app\Handlers\Admin\Pages\ListHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\permission_middleware;

class ListController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_PAGES_CRUD_ACCESS));
    }

    public function pagination(Request $request, ListHandler $handler)
    {
        $dto = $handler->handle(
            (new PaginationList())
                ->setOrderBy($request->get('order_by') ?? 'id')
                ->setDescending((bool)$request->get('descending'))
                ->setSearch($request->get('search'))
                ->setPage((int)($request->get('page') ?? 1))
                ->setPerPage((int)($request->get('per_page') ?? 25))
        );

        return new JsonResponse(Status::SUCCESS, [
            'paginator' => $dto->getPaginator(),
            'pages' => $dto->getPages()
        ]);
    }

    public function delete(Request $request, DeleteHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->get('page'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.admin.pages.list.delete.success')));
        } catch (PageNotFoundException $e) {
            return (new JsonResponse('page_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.pages.list.delete.not_found')));
        }
    }
}
