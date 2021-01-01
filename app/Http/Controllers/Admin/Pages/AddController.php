<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Pages;

use app\DataTransferObjects\Admin\Pages\Add;
use app\Exceptions\Page\AlreadyExistException;
use app\Handlers\Admin\Pages\AddHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Pages\AddEditRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notifications\Warning;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Response;
use function app\permission_middleware;

class AddController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_PAGES_CRUD_ACCESS));
    }

    public function add(AddEditRequest $request, AddHandler $handler): JsonResponse
    {
        $dto = (new Add())
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'))
            ->setUrl($request->get('url'));

        try {
            $handler->handle($dto);

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.pages.add.success')));
        } catch (AlreadyExistException $e) {
            return (new JsonResponse('page_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Warning(__('msg.admin.pages.add.already_exists')));
        }
    }
}
