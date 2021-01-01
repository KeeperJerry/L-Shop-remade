<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Pages;

use app\DataTransferObjects\Admin\Pages\Edit\Edit;
use app\Exceptions\Page\PageNotFoundException;
use app\Handlers\Admin\Pages\Edit\EditHandler;
use app\Handlers\Admin\Pages\Edit\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Pages\AddEditRequest;
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
        $this->middleware(permission_middleware(Permissions::ADMIN_PAGES_CRUD_ACCESS));
    }

    public function render(Request $request, RenderHandler $handler): JsonResponse
    {
        $page = $handler->handle((int)$request->route('page'));

        return new JsonResponse(Status::SUCCESS, [
            'page' => $page
        ]);
    }

    public function edit(AddEditRequest $request, EditHandler $handler): JsonResponse
    {
        $dto = (new Edit())
            ->setId((int)$request->route('page'))
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'))
            ->setUrl($request->get('url'));
        try {
            $handler->handle($dto);

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.pages.edit.success')));
        } catch (PageNotFoundException $e) {
            return (new JsonResponse('page_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.pages.edit.not_found')));
        }
    }
}
