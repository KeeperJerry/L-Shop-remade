<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\News;

use app\DataTransferObjects\PaginationList;
use app\Handlers\Admin\News\ListHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Permissions;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use function app\permission_middleware;

class ListController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_NEWS_CRUD_ACCESS));
    }

    public function pagination(Request $request, ListHandler $handler): JsonResponse
    {
        $dto = $handler->handle(
            (new PaginationList())
                ->setOrderBy($request->get('order_by'))
                ->setDescending((bool)$request->get('descending'))
                ->setSearch($request->get('search'))
                ->setPage((int)$request->get('page'))
                ->setPerPage((int)$request->get('per_page'))
        );

        return new JsonResponse(Status::SUCCESS, $dto);
    }
}
