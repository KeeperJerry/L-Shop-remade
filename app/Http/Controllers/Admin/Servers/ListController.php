<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Servers;

use app\Exceptions\Server\ServerNotFoundException;
use app\Handlers\Admin\Servers\DeleteHandler;
use app\Handlers\Admin\Servers\ListHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use function app\permission_middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ListController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_SERVERS_CRUD_ACCESS));
    }

    public function render(ListHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, [
            'servers' => $handler->handle()
        ]);
    }

    public function delete(Request $request, DeleteHandler $handler): JsonResponse
    {
        try {
            return (new JsonResponse(Status::SUCCESS, $handler->handle((int)$request->route('server'))))
                ->addNotification(new Info(__('msg.admin.servers.delete.success')));
        } catch (ServerNotFoundException $e) {
            return (new JsonResponse('server_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.servers.delete.not_found')));
        }
    }
}
