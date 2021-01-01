<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\News;

use app\DataTransferObjects\Admin\News\Add;
use app\Handlers\Admin\News\AddHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\News\AddEditRequest;
use function app\permission_middleware;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;

class AddController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_NEWS_CRUD_ACCESS));
    }

    public function render(): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS);
    }

    public function add(AddEditRequest $request, AddHandler $handler): JsonResponse
    {
        $handler->handle(new Add($request->get('title'), $request->get('content')));

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('msg.admin.news.add.success')));
    }
}
