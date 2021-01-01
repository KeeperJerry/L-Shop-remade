<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\News;

use app\DataTransferObjects\Admin\News\EditNews;
use app\Exceptions\News\NewsNotFoundException;
use app\Handlers\Admin\News\DeleteHandler;
use app\Handlers\Admin\News\Edit\EditHandler;
use app\Handlers\Admin\News\Edit\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\News\AddEditRequest;
use function app\permission_middleware;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EditController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_NEWS_CRUD_ACCESS));
    }

    public function render(Request $request, RenderHandler $handler): JsonResponse
    {
        try {
            return new JsonResponse(Status::SUCCESS, $handler->handle((int)$request->route('news')));
        } catch (NewsNotFoundException $e) {
            return (new JsonResponse('not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND);
        }
    }

    public function edit(AddEditRequest $request, EditHandler $handler): JsonResponse
    {
        $dto = new EditNews(
            (int)$request->route('news'),
            $request->get('title'),
            $request->get('content')
        );

        try {
            $handler->handle($dto);

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.news.edit.success')));
        } catch (NewsNotFoundException $e) {
            return (new JsonResponse('not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.news.edit.not_found', ['id' => $dto->getId()])));
        }
    }

    public function delete(Request $request, DeleteHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('news'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.admin.news.list.delete.success')));
        } catch (NewsNotFoundException $e) {
            return (new JsonResponse('not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.news.list.delete.not_found')));
        }
    }
}
